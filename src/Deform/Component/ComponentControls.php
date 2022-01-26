<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Html\HtmlTag;
use Deform\Html\IHtml;

/**
 * a component can contain multiple controls (a control is the "active" part of the component such as an input)
 */
class ComponentControls
{
    /** @var HtmlTag[] */
    private array $controlTags = [];

    /** @var array */
    private array $allTags = [];

    /** @var HtmlTag[]  */
    private array $tagsWithForById = [];

    /**
     * specifies an additional control for this set of controls, if any decoration is included you should ensure it
     * also contains somewhere inside a copy of the actual control itself.
     * @param HtmlTag $controlTag
     * @param HtmlTag|string|IHtml|array|null $controlDecoration
     * @throws \Exception
     */
    public function addControl(HtmlTag $controlTag, $controlDecoration = null)
    {
        if (!$controlTag->has('id')) {
            throw new \Exception("The control tag must contain an 'id'");
        }
        $id = $controlTag->get('id');
        $this->controlTags[] = $controlTag;
        if ($controlDecoration != null) {
            if (is_array($controlDecoration)) {
                foreach ($controlDecoration as $decoration) {
                    if ($decoration instanceof HtmlTag) {
                        if ($decoration->has('for')) {
                            if (!isset($tagsWithForById[$id])) {
                                $tagsWithForById = [];
                            }
                            $tagsWithForById[$id][] = $decoration;
                        }
                    }
                }
                $this->allTags = array_merge($this->allTags, $controlDecoration);
            } else {
                array_push($this->allTags, $controlDecoration);
            }
        } else {
            $this->allTags[] = $controlTag;
        }
    }

    /**
     * @param string $newId
     * @param string $newName
     * @throws \Exception
     */
    public function changeNamespaceAttributes(string $newId, string $newName)
    {
        foreach ($this->controlTags as $control) {
            $oldId = $control->get('id');
            $control->setIfExists('id', $newId);
            $control->setIfExists('name', $newName);
            if (isset($this->tagsWithForById[$oldId])) {
                foreach ($this->tagsWithForById[$oldId] as $htmlTag) {
                    $htmlTag->setIfExists('for', $newId);
                }
            }
        }
    }

    /**
     * @return HtmlTag[]
     */
    public function getControls()
    {
        return $this->controlTags;
    }

    /**
     * @return array
     */
    public function getHtmlTags()
    {
        return $this->allTags;
    }
}
