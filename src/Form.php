<?php

namespace Dcat\Admin\FormStep;

use Dcat\Admin\Admin;
use Dcat\Admin\Form as ParentForm;
use Dcat\Admin\Widgets\Form as WidgetForm;

class Form extends WidgetForm
{
    /**
     * @var string
     */
    protected $view = 'joseph-bing-han.dcat-form-step::form';

    /**
     * @var array
     */
    protected $buttons = [];

    /**
     * @var ParentForm
     */
    protected $form;

    /**
     * @var Builder
     */
    protected $parent;

    /**
     * @var int
     */
    protected $index;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * FormStep constructor.
     *
     * @param ParentForm $form
     * @param string     $title
     * @param int        $index
     */
    public function __construct(ParentForm $form, string $title = null, int $index = 0)
    {
        $this->setForm($form);
        $this->initFields();

        $this->setTitle($title);
        $this->setIndex($index);
    }

    /**
     * @param ParentForm $form
     *
     * @return $this
     */
    protected function setForm(?ParentForm $form)
    {
        $this->form = $form;
        $this->parent = $form->multipleSteps();

        $this->prepareFileFields();

        return $this;
    }

    /**
     * @param string|\Closure $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = value($title);

        return $this;
    }

    /**
     * @return string
     */
    public function title()
    {
        return $this->title;
    }

    /**
     * @param string|\Closure $content
     *
     * @return $this
     */
    public function setDescription($content)
    {
        $this->description = value($content);

        return $this;
    }

    /**
     * @return string
     */
    public function description()
    {
        return $this->description;
    }

    /**
     * @param int $content
     *
     * @return $this
     */
    public function setIndex(int $index = null)
    {
        $this->index = $index;

        return $this;
    }

    /**
     * @return int
     */
    public function index()
    {
        return $this->index;
    }

    /**
     * @return string
     */
    protected function open()
    {
        if ($this->index > 0) {
            $this->setHtmlAttribute('style', 'display:none');
        }

        $this->setHtmlAttribute('data-toggle', 'validator');
        $this->setHtmlAttribute('role', 'form');

        return <<<HTML
<div {$this->formatHtmlAttributes()}>
HTML;
    }

    /**
     * @return string
     */
    protected function close()
    {
        return '</div>';
    }

    /**
     * @return void
     */
    protected function fillStash()
    {
        if ($this->data) {
            return;
        }

        if ($input = $this->parent->fetchStash()) {
            $this->fill($input);
        }
    }

    /**
     * @return void
     */
    protected function prepareFileFields()
    {
        $this->form->uploaded(function (ParentForm $form, ParentForm\Field $field, $file, $response) {
            if (($value = $response->toArray()) && ! empty($value['id'])) {
                $form->multipleSteps()->stash(
                    [$field->column() => $value['id']],
                    true
                );
            }
        });
    }

    /**
     * @return string
     */
    public function render()
    {
        $this->fillStash();

        Admin::requireAssets('@joseph-bing-han.dcat-form-step');

        return parent::render(); // TODO: Change the autogenerated stub
    }

    /**
     * @param string $script
     *
     * @return $this
     */
    public function leaving($script)
    {
        $script = value($script);

        $this->parent->leaving(
            <<<JS
if (args.index == {$this->index}) {
    {$script}
}
JS
        );

        return $this;
    }

    /**
     * @param string $script
     *
     * @return $this
     */
    public function shown($script)
    {
        $script = value($script);

        $this->parent->shown(
            <<<JS
if (args.index == {$this->index}) {
    {$script}
}
JS
        );

        return $this;
    }
}
