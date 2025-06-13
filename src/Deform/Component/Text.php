<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Exception\DeformException;
use Deform\Html\Html;

/**
 * @method $this maxlength(int $maxLength)
 * @method $this minlength(int $maxLength)
 * @method $this pattern(int $pattern)
 * @method $this placeholder(string $text)
 * @method $this size(int $chars)
 * @persistAttribute datalist
 * @persistAttribute datalistId
 */
class Text extends Input
{
    private array $datalist = [];
    private ?string $datalistId = null;

    /**
     * @inheritDoc
     */
    public function setup(): void
    {
        parent::setup();
        $this->input->type('text');
    }

    /**
     * @param array $datalist
     * @param string|null $datalistId
     * @return static
     * @throws DeformException
     */
    public function datalist(array $datalist, string $datalistId = null): static
    {
        if (!$datalistId) {
            $datalistId = $this->input->get('name') . '-datalist';
        }
        $this->datalist = $datalist;
        $this->datalistId = $datalistId;

        $datalistHtml = Html::datalist()->id($this->datalistId);
        foreach ($this->datalist as $value) {
            $datalistHtml->add(Html::option()->value($value));
        }
        $this->input->set('list', $this->datalistId);
        $this->input->set('onmouseover', 'focus()');

        $this->addControl($datalistHtml);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hydrate(): void
    {
        if (is_array($this->datalist) && count($this->datalist) > 0) {
            $this->datalist($this->datalist, $this->datalistId);
        }
    }
}
