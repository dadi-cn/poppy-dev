<?php

namespace Demo\App\Forms;

use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Validation\Rule;
use Poppy\MgrApp\Classes\Widgets\FormWidget;

class FormFieldSelectEstablish extends FormWidget
{

    public function handle()
    {
        $message = print_r(input(), true);
        return Resp::success($message);
    }

    /**
     */
    public function data(): array
    {
        return [
            'id'     => 5,
            'select' => 'a',

            'select-code'        => <<<CODE
\$this->select('select', '单选')->options([
    'a' => 'A',
    'b' => 'B',
]);
CODE,
            'placeholder-code'   => <<<CODE
\$this->select('placeholder', '单选')->options([
    'a' => 'A',
    'b' => 'B',
])->placeholder('占位内容');
CODE,
            'disabled-code'      => <<<CODE
\$this->select('disabled', '禁用')->options([
    'a' => 'A',
    'b' => 'B',
])->disabled();
CODE,
            'complex-code'       => <<<CODE
\$this->select('complex', '单选/复杂模式')->options([
    ['value' => 'a', 'disabled' => false, 'label' => 'Label A'],
    ['value' => 'b', 'disabled' => true, 'label' => 'Label B'],
]);
CODE,
            'group-code'         => <<<CODE
\$this->select('group', '分组')->options([
    [
        'label'   => 'GroupA',
        'options' => [
            ['value' => 'a1', 'disabled' => false, 'label' => 'A-1'],
            ['value' => 'a2', 'disabled' => true, 'label' => 'A-2'],
        ],
    ],
    [
        'label'   => 'GroupB',
        'options' => [
            ['value' => 'b1', 'disabled' => false, 'label' => 'B-1'],
            ['value' => 'b2', 'disabled' => true, 'label' => 'B-2'],
        ],
    ],

]);


CODE,
            'multi-code'         => <<<CODE
\$this->multiSelect('multi', '多选:禁用')->options([
    'a' => 'A',
    'b' => 'B',
]);
CODE,
            'multi-complex-code' => <<<CODE
\$this->multiSelect('multi-complex', '多选:复杂模式')->options([
    ['value' => 'a', 'disabled' => false, 'label' => 'Label A'],
    ['value' => 'b', 'disabled' => true, 'label' => 'Label B'],
]);
CODE,
            'multi-group-code'   => <<<CODE
\$this->multiSelect('multi-group', '多选:分组')->options([
    [
        'label'   => 'GroupA',
        'options' => [
            ['value' => 'a1', 'disabled' => false, 'label' => 'A-1'],
            ['value' => 'a2', 'disabled' => false, 'label' => 'A-2'],
        ],
    ],
    [
        'label'   => 'GroupB',
        'options' => [
            ['value' => 'b1', 'disabled' => false, 'label' => 'B-1'],
            ['value' => 'b2', 'disabled' => false, 'label' => 'B-2'],
        ],
    ],

])->rules([
    Rule::max(1)
]);

CODE,
            'tags-code'          => <<<CODE
\$this->tags('tags', '标签:禁用')->options([
    'a' => 'A',
    'b' => 'B',
]);
CODE,
            'tags-complex-code'  => <<<CODE
\$this->tags('tags-complex', '标签:复杂模式')->options([
    ['value' => 'a', 'disabled' => false, 'label' => 'Label A'],
    ['value' => 'b', 'disabled' => true, 'label' => 'Label B'],
]);
CODE,
            'tags-group-code'    => <<<CODE
\$this->tags('tags-group', '标签:分组')->options([
    [
        'label'   => 'GroupA',
        'options' => [
            ['value' => 'a1', 'disabled' => false, 'label' => 'A-1'],
            ['value' => 'a2', 'disabled' => false, 'label' => 'A-2'],
        ],
    ],
    [
        'label'   => 'GroupB',
        'options' => [
            ['value' => 'b1', 'disabled' => false, 'label' => 'B-1'],
            ['value' => 'b2', 'disabled' => false, 'label' => 'B-2'],
        ],
    ],

])->rules([
    Rule::max(1)
]);
CODE,
        ];
    }

    public function form()
    {
        $this->select('select', '单选')->options([
            'a' => 'A',
            'b' => 'B',
        ]);
        $this->code('select-code');
        $this->select('placeholder', '单选')->options([
            'a' => 'A',
            'b' => 'B',
        ])->placeholder('占位内容');
        $this->code('placeholder-code');
        $this->select('disabled', '禁用')->options([
            'a' => 'A',
            'b' => 'B',
        ])->disabled();
        $this->code('disabled-code');
        $this->select('complex', '单选/复杂模式')->options([
            ['value' => 'a', 'disabled' => false, 'label' => 'Label A'],
            ['value' => 'b', 'disabled' => true, 'label' => 'Label B'],
        ]);
        $this->code('complex-code');
        $this->select('group', '分组')->options([
            [
                'label'   => 'GroupA',
                'options' => [
                    ['value' => 'a1', 'disabled' => false, 'label' => 'A-1'],
                    ['value' => 'a2', 'disabled' => true, 'label' => 'A-2'],
                ],
            ],
            [
                'label'   => 'GroupB',
                'options' => [
                    ['value' => 'b1', 'disabled' => false, 'label' => 'B-1'],
                    ['value' => 'b2', 'disabled' => true, 'label' => 'B-2'],
                ],
            ],

        ]);
        $this->code('group-code');


        $this->multiSelect('multi', '多选:禁用')->options([
            'a' => 'A',
            'b' => 'B',
        ]);
        $this->code('multi-code');
        $this->multiSelect('multi-complex', '多选:复杂模式')->options([

            ['value' => 'a', 'disabled' => false, 'label' => 'Label A'],
            ['value' => 'b', 'disabled' => true, 'label' => 'Label B'],
        ]);
        $this->code('multi-complex-code');
        $this->multiSelect('multi-group', '多选:分组')->options([

            [
                'label'   => 'GroupA',
                'options' => [
                    ['value' => 'a1', 'disabled' => false, 'label' => 'A-1'],
                    ['value' => 'a2', 'disabled' => false, 'label' => 'A-2'],
                ],
            ],
            [
                'label'   => 'GroupB',
                'options' => [
                    ['value' => 'b1', 'disabled' => false, 'label' => 'B-1'],
                    ['value' => 'b2', 'disabled' => false, 'label' => 'B-2'],
                ],
            ],

        ])->rules([
            Rule::max(1)
        ]);
        $this->code('multi-group-code');

        $this->tags('tags', '标签:禁用')->options([
            'a' => 'A',
            'b' => 'B',
        ]);
        $this->code('tags-code');
        $this->tags('tags-complex', '标签:复杂模式')->options([

            ['value' => 'a', 'disabled' => false, 'label' => 'Label A'],
            ['value' => 'b', 'disabled' => true, 'label' => 'Label B'],
        ]);
        $this->code('tags-complex-code');
        $this->tags('tags-group', '标签:分组')->options([

            [
                'label'   => 'GroupA',
                'options' => [
                    ['value' => 'a1', 'disabled' => false, 'label' => 'A-1'],
                    ['value' => 'a2', 'disabled' => false, 'label' => 'A-2'],
                ],
            ],
            [
                'label'   => 'GroupB',
                'options' => [
                    ['value' => 'b1', 'disabled' => false, 'label' => 'B-1'],
                    ['value' => 'b2', 'disabled' => false, 'label' => 'B-2'],
                ],
            ],

        ])->rules([
            Rule::max(1)
        ]);
        $this->code('tags-group-code');

        $this->select('select-searchable', '可搜索')->options([
            'a' => 'A',
            'b' => 'B',
        ])->filterable();
    }
}
