<?php

namespace Botble\Marketplace\Forms;


use Botble\Base\Forms\Fields\MultiCheckListField;
use Botble\Base\Forms\Fields\TagField;
use Botble\Base\Supports\Helper;
use Botble\Base\Supports\Language;
use Botble\Blog\Models\Category;
use Botble\Ecommerce\Forms\Fields\CategoryMultiField;
use Botble\Ecommerce\Forms\ProductForm as BaseProductForm;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Ecommerce\Repositories\Interfaces\BrandInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductAttributeInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductAttributeSetInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductCollectionInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductVariationInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductVariationItemInterface;
use Botble\Ecommerce\Repositories\Interfaces\TaxInterface;
use Botble\Marketplace\Forms\Fields\CustomEditorField;
use Botble\Marketplace\Forms\Fields\CustomImagesField;
use Botble\Marketplace\Http\Requests\ProductRequest;
use EcommerceHelper;
use Illuminate\Support\Collection;
use MarketplaceHelper;

class ProductForm extends BaseProductForm
{


    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
        $countries = Helper::countries();
        $languages= Language::getListLanguages();
        $selectedCategories = [];
        if ($this->getModel()) {
            $selectedCategories = $this->getModel()->categories()->pluck('category_id')->all();
            asort($selectedCategories);
            $data['categories']['selectedCategories'] = $selectedCategories;
            $data['categories']['sub_1_category'] = ProductCategory::query()->find($selectedCategories[1]);
            $data['categories']['sub_2_category'] = ProductCategory::query()->find($selectedCategories[2]);
        }

        $brands = app(BrandInterface::class)->pluck('name', 'id');

        $brands = [0 => trans('plugins/ecommerce::brands.no_brand')] + $brands;

        $productCollections = app(ProductCollectionInterface::class)->pluck('name', 'id');

        $selectedProductCollections = [];
        $product = null;
        if ($this->getModel()) {
            $selectedProductCollections = $this->getModel()->productCollections()->pluck('product_collection_id')
                ->all();
            $product = $this->getModel();
        }

        $productId = $this->getModel() ? $this->getModel()->id : null;

        $productAttributeSets = app(ProductAttributeSetInterface::class)->getAllWithSelected($productId);

        $productVariations = [];

        if ($this->getModel()) {
            $productVariations = app(ProductVariationInterface::class)->allBy([
                'configurable_product_id' => $this->getModel()->id,
            ]);
        }

        $tags = null;

        if ($this->getModel()) {
            $tags = $this->getModel()->tags()->pluck('name')->all();
            $tags = implode(',', $tags);
        }

        $this
            ->setupModel(new Product)
            ->withCustomFields()
            ->addCustomField('customEditor', CustomEditorField::class)
            ->addCustomField('customImages', CustomImagesField::class)
            ->addCustomField('categoryMulti', CategoryMultiField::class)
            ->addCustomField('multiCheckList', MultiCheckListField::class)
            ->addCustomField('tags', TagField::class)
            ->setFormOption('template', MarketplaceHelper::viewPath('dashboard.forms.base'))
            ->setFormOption('enctype', 'multipart/form-data')
            ->setValidatorClass(ProductRequest::class)
            ->setActionButtons(MarketplaceHelper::view('dashboard.forms.actions')->render())
            ->add('name', 'text', [
                'label'      => trans('plugins/ecommerce::products.form.name'),
                'label_attr' => ['class' => 'text-title-field required'],
                'attr'       => [
                    'placeholder'  => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            // ->add('description', 'customEditor', [
            //     'label'      => trans('core/base::forms.description'),
            //     'label_attr' => ['class' => 'control-label'],
            //     'attr'       => [
            //         'rows'         => 2,
            //         'placeholder'  => trans('core/base::forms.description_placeholder'),
            //         'data-counter' => 1000,
            //     ],
            // ])
            ->add('content', 'customEditor', [
                'label'      => trans('core/base::forms.content'),
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'rows' => 4,
                ],
            ])
            ->add('deliverables', 'editor', [
                'label'      => trans('plugins/ecommerce::products.form.deliverables'),
                'label_attr' => ['class' => 'text-title-field'],
                'attr'       => [
                    'rows'            => 4,
                    'with-short-code' => true,
                    'required' => 'required',
                ],
            ])
            ->add('images', 'customImages', [
                'label'      => trans('plugins/ecommerce::products.form.image'),
                'label_attr' => ['class' => 'control-label required'],
                'values'     => $productId ? $this->getModel()->images : [],
                'required' => 'required',
            ])
            ->add('categories[]', 'categoryMulti', [
                'label'      => trans('plugins/ecommerce::products.form.categories'),
                'label_attr' => ['class' => 'control-label'],
                'choices' => get_product_categories_with_children(),
                'value'      =>  $data['categories'],
            ])
            ->add('brand_id', 'customSelect', [
                'label'      => trans('plugins/ecommerce::products.form.brand'),
                'label_attr' => ['class' => 'control-label'],
                'choices'    => $brands,
            ]);
            // ->add('product_collections[]', 'multiCheckList', [
            //     'label'      => trans('plugins/ecommerce::products.form.collections'),
            //     'label_attr' => ['class' => 'control-label'],
            //     'choices'    => $productCollections,
            //     'value'      => old('product_collections', $selectedProductCollections),
            // ]);

        $this
            ->add('tag', 'tags', [
                'label'      => trans('plugins/ecommerce::products.form.tags'),
                'label_attr' => ['class' => 'control-label'],
                'value'      => $tags,
                'attr'       => [
                    'placeholder' => trans('plugins/ecommerce::products.form.write_some_tags'),
                    'data-url'    => route('marketplace.vendor.tags.all'),
                ],
            ])
            ->addMetaBoxes(['Image' => [
                'title' => 'Featured Image',
                'content' =>  MarketplaceHelper::view('custom.product-featured-image' , ['product' => $product])
            ]])
            ->setBreakFieldPoint('categories[]');

        if (empty($productVariations) || $productVariations->isEmpty()) {
            $attributeSetId = $productAttributeSets->first() ? $productAttributeSets->first()->id : 0;
            $this
                ->removeMetaBox('variations')
                ->addMetaBoxes([
                    'general'    => [
                        'title'          => trans('plugins/ecommerce::products.overview'),
                        'content'        => view('plugins/ecommerce::products.partials.general',
                            [
                                'product' => $productId ? $this->getModel() : null,
                                'isVariation' => false,
                            ])
                            ->render(),
                        'before_wrapper' => '<div id="main-manage-product-type">',
                        'priority'       => 2,
                    ],
                    'attributes' => [
                        'title'         => trans('plugins/ecommerce::products.attributes'),
                        'content'       => view('plugins/ecommerce::products.partials.add-product-attributes', [
                            'productAttributeSets' => $productAttributeSets,
                            'productAttributes'    => $this->getProductAttributes($attributeSetId),
                            'product'              => $productId,
                            'attributeSetId'       => $attributeSetId,
                            'required' => 'required',
                        ])->render(),
                        'after_wrapper' => '</div>',
                        'priority'      => 3,
                    ],
                ])
                ->addMetaBoxes(['Basic Product Attributes' => [
                    'title' => trans('plugins/ecommerce::products.form.Basic Product Attributes'),
                    'content' => view('plugins/ecommerce::products.partials.basic-product-attributes' , compact('countries' ,'product' , 'languages')),
                ]])
                ;
        } elseif ($productId) {
            $productVariationsInfo = [];
            $productsRelatedToVariation = [];

            if ($this->getModel()) {
                $productVariationsInfo = app(ProductVariationItemInterface::class)
                    ->getVariationsInfo($productVariations->pluck('id')->toArray());

                $productsRelatedToVariation = app(ProductInterface::class)->getProductVariations($productId);
            }
            $this
                ->removeMetaBox('general')
                ->removeMetaBox('attributes')
                ->addMetaBoxes([
                    'variations' => [
                        'title'          => trans('plugins/ecommerce::products.product_has_variations'),
                        'content'        => MarketplaceHelper::view('dashboard.products.configurable', [
                            'productAttributeSets'       => $productAttributeSets,
                            'productVariations'          => $productVariations,
                            'productVariationsInfo'      => $productVariationsInfo,
                            'productsRelatedToVariation' => $productsRelatedToVariation,
                            'product'                    => $this->getModel(),
                        ])->render(),
                        'before_wrapper' => '<div id="main-manage-product-type">',
                        'after_wrapper'  => '</div>',
                        'priority'       => 4,
                    ],
                ]);
        }
    }

    /**
     * @return Collection
     */
    public function getProductAttributes($attributeSetId)
    {
        $params = ['order_by' => ['ec_product_attributes.order' => 'ASC']];

        if ($attributeSetId) {
            $params['condition'] = [['attribute_set_id', '=', $attributeSetId]];
        }

        return app(ProductAttributeInterface::class)->advancedGet($params);
    }
}
