<?php

namespace Poppy\MgrPage\Classes\Grid\Tools;

use Poppy\MgrPage\Classes\Grid;
use function request;
use function trans;
use function view;

class ExportButton extends AbstractTool
{
    /**
     * @var Grid
     */
    protected $grid;

    /**
     * Create a new Export button instance.
     *
     * @param Grid $grid
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * Render Export button.
     *
     * @return string
     */
    public function render()
    {
        if (!$this->grid->isShowExporter()) {
            return '';
        }

        $page      = request('page', 1);
        $variables = [
            'filter_id'            => $this->grid->getFilter()->getFilterId(),
            'export'               => trans('admin.export'),
            'all'                  => trans('admin.all'),
            'all_url'              => $this->grid->getExportUrl('all'),
            'current_page_url'     => $this->grid->getExportUrl('page', $page),
            'current_page'         => trans('admin.current_page'),
            'selected_rows'        => trans('admin.selected_rows'),
            'selected_rows_url'    => $this->grid->getExportUrl('selected', '__rows__'),
            'selected_export_name' => $this->grid->getExportSelectedName(),
        ];

        return view('py-mgr-page::tpl.filter.export-button', $variables);
    }
}
