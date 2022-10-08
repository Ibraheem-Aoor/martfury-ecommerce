<?php

namespace Botble\Marketplace\Forms;


use Botble\Base\Forms\Fields\MultiCheckListField;
use Botble\Base\Forms\Fields\TagField;
use Botble\Base\Supports\Helper;
use Botble\Ecommerce\Forms\Fields\CategoryMultiField;
use Botble\Ecommerce\Forms\ProductForm as BaseProductForm;
use Botble\Ecommerce\Models\Product;
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

class ProductForm2 extends BaseProductForm
{


    public function __construct()
    {
        $this->formOptions['url'] = route('marketplace.vendor.products.create');
        $this->formOptions['method'] = 'POST';
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
        $countries = Helper::countries();
        $selectedCategories = [];
        if ($this->getModel()) {
            $selectedCategories = $this->getModel()->categories()->pluck('category_id')->all();
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
            ->add('images', 'customImages', [
                'label'      => trans('plugins/ecommerce::products.form.image'),
                'label_attr' => ['class' => 'control-label required'],
                'values'     => $productId ? $this->getModel()->images : [],
                'required' => 'required',
            ]);
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
