<?php

namespace Botble\Marketplace\Tables;

use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Repositories\Interfaces\ReviewInterface;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use RvMedia;

class ReviewTable extends TableAbstract
{

    /**
     * @var bool
     */
    protected $hasActions = false;

    /**
     * @var bool
     */
    protected $hasFilter = false;

    /**
     * @var bool
     */
    protected $hasOperations = false;

    /**
     * @var bool
     */
    protected $hasCheckbox = false;

    /**
     * ReviewTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param ReviewInterface $reviewRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, ReviewInterface $reviewRepository)
    {
        $this->repository = $reviewRepository;
        parent::__construct($table, $urlGenerator);
    }

    /**
     * {@inheritDoc}
     */
    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('product_id', function ($item) {
                if (!empty($item->product)) {
                    return Html::link($item->product->url,
                        clean($item->product_name),
                        ['target' => '_blank']
                    );
                }
                return null;
            })
            ->editColumn('customer_id', function ($item) {
                return clean($item->user->name);
            })
            ->editColumn('star', function ($item) {
                return view('plugins/ecommerce::reviews.partials.rating', ['star' => $item->star])->render();
            })
            ->editColumn('images', function ($item) {
                $count = count($item->images);

                if ($count == 0) {
                    return '&mdash;';
                }

                $galleryID = 'images-group-' . $item->id;

                $html = Html::image(RvMedia::getImageUrl($item->images[0], 'thumb'),
                    RvMedia::getImageUrl($item->images[0]), [
                        'width'         => 60,
                        'class'         => 'fancybox m-1 rounded-top rounded-end rounded-bottom rounded-start border d-inline-block',
                        'href'          => RvMedia::getImageUrl($item->images[0]),
                        'data-fancybox' => $galleryID,
                    ]);

                if (isset($item->images[1])) {
                    if ($count == 2) {
                        $html .= Html::image(RvMedia::getImageUrl($item->images[1], 'thumb'),
                            RvMedia::getImageUrl($item->images[1]), [
                                'width'         => 60,
                                'class'         => 'fancybox m-1 rounded-top rounded-end rounded-bottom rounded-start border d-inline-block',
                                'href'          => RvMedia::getImageUrl($item->images[1]),
                                'data-fancybox' => $galleryID,
                            ]);
                    } elseif ($count > 2) {
                        $html .= Html::tag('a', Html::image(RvMedia::getImageUrl($item->images[1], 'thumb'),
                                RvMedia::getImageUrl($item->images[1]), [
                                    'width' => 60,
                                    'class' => 'm-1 rounded-top rounded-end rounded-bottom rounded-start border',
                                    'src'   => RvMedia::getImageUrl($item->images[1]),
                                ])->toHtml() . Html::tag('span', '+' . ($count - 2))->toHtml(), [
                            'class'         => 'fancybox more-review-images',
                            'href'          => RvMedia::getImageUrl($item->images[1]),
                            'data-fancybox' => $galleryID,
                        ]);
                    }
                }

                if ($count > 2) {
                    foreach ($item->images as $index => $image) {
                        if ($index > 1) {
                            $html .= Html::image(RvMedia::getImageUrl($item->images[$index], 'thumb'),
                                RvMedia::getImageUrl($item->images[$index]), [
                                    'width'         => 60,
                                    'class'         => 'fancybox d-none',
                                    'href'          => RvMedia::getImageUrl($item->images[$index]),
                                    'data-fancybox' => $galleryID,
                                ]);
                        }
                    }
                }

                return $html;

            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            });

        return $this->toJson($data);
    }

    /**
     * {@inheritDoc}
     */
    public function query()
    {
        $query = $this->repository->getModel()
            ->select([
                'ec_reviews.id',
                'ec_reviews.star',
                'ec_reviews.comment',
                'ec_reviews.product_id',
                'ec_reviews.customer_id',
                'ec_reviews.status',
                'ec_reviews.created_at',
                'ec_reviews.images',
            ])
            ->with(['user', 'product'])
            ->join('ec_products', 'ec_products.id', 'ec_reviews.product_id')
            ->where([
                'ec_products.store_id' => auth('customer')->user()->store->id,
                'ec_reviews.status'    => BaseStatusEnum::PUBLISHED,
                'ec_products.status'   => BaseStatusEnum::PUBLISHED,
            ]);

        return $this->applyScopes($query);
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'id'          => [
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
                'class' => 'text-start',
            ],
            'product_id'  => [
                'title' => trans('plugins/ecommerce::review.product'),
                'class' => 'text-start',
            ],
            'customer_id' => [
                'title' => trans('plugins/ecommerce::review.user'),
                'class' => 'text-start',
            ],
            'star'        => [
                'title' => trans('plugins/ecommerce::review.star'),
                'class' => 'text-center',
            ],
            'comment'     => [
                'title' => trans('plugins/ecommerce::review.comment'),
                'class' => 'text-start',
            ],
            'images'     => [
                'title'      => trans('plugins/ecommerce::review.images'),
                'width'      => '150px',
                'class'      => 'text-start',
                'searchable' => false,
                'orderable'  => false,
            ],
            'created_at'  => [
                'title' => trans('core/base::tables.created_at'),
                'width' => '70px',
                'class' => 'text-start',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function htmlDrawCallbackFunction(): ?string
    {
        return parent::htmlDrawCallbackFunction() . 'if (jQuery().fancybox) {
            $(".dataTables_wrapper .fancybox").fancybox({
                openEffect: "none",
                closeEffect: "none",
                overlayShow: true,
                overlayOpacity: 0.7,
                helpers: {
                    media: {}
                },
            });
        }';
    }
}
