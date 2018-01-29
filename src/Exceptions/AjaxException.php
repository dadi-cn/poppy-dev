<?php namespace Poppy\Framework\Exceptions;


class AjaxException extends \Exception
{

    /**
     * @var array Collection response contents.
     */
    protected $contents;

    /**
     * Constructor.
     */
    public function __construct($contents)
    {
        if (is_string($contents)) {
            $contents = ['result' => $contents];
        }

        $this->contents = $contents;

        parent::__construct(json_encode($contents));
    }

    /**
     * Returns invalid fields.
     */
    public function getContents()
    {
        return $this->contents;
    }

}
