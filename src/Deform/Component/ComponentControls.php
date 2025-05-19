<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Exception\DeformComponentException;
use Deform\Exception\DeformException;
use Deform\Html\HtmlTag;
use Deform\Html\IHtml;

/**
 * a component can contain multiple controls (a control is the "active" part of the component such as an input, select,
 * textarea or button)
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
     * specifies an additional control for this set of controls.
     *
     * if the decoration is included you should ensure it
     * also contains somewhere inside a copy of the actual control itself.
     *
     * todo: - consider distinguishing multiple control tags with decoration from single ones
     * i.e.
     *    multi checkbox has multiple controls each with multiple decorations (labels etc)
     *    single checkbox has one control with multiple decorations
     *
     * @param HtmlTag $controlTag
     * @param array|string|HtmlTag|IHtml|null $controlDecoration
     * @throws DeformException
     */
    public function addControl(HtmlTag $controlTag, HtmlTag|IHtml|array|string $controlDecoration = null): void
    {
        if (!$controlTag->has('id')) {
            throw new DeformComponentException("The control tag must contain an 'id'");
        }
        $id = $controlTag->get('id');
        $this->controlTags[] = $controlTag;
        if ($controlDecoration != null) {
            if (is_array($controlDecoration)) {
                $containsControlTag = false;
                foreach ($controlDecoration as $decoration) {
                    if ($decoration instanceof HtmlTag) {
                        if ($decoration === $controlTag) {
                            $containsControlTag = true;
                        }
                        if ($decoration->has('for')) {
                            if (!isset($this->tagsWithForById[$id])) {
                                $this->tagsWithForById[$id] = [];
                            }
                            $this->tagsWithForById[$id][] = $decoration;
                        }
                    }
                }
                if (!$containsControlTag) {
                    throw new DeformComponentException(
                        "When adding decoration as an array, one of the elements must be the control tag itself"
                    );
                }
                $this->allTags = array_merge($this->allTags, $controlDecoration);
            } else {
                $this->allTags[] = $controlDecoration;
            }
        } else {
            $this->allTags[] = $controlTag;
        }
    }

    /**
     * @return void
     */
    public function clearControls(): void
    {
        $this->controlTags = [];
        $this->allTags = [];
        $this->tagsWithForById = [];
    }


    /**
     * add some arbitrary html to the control container
     * @param HtmlTag $htmlTag
     * @return void
     */
    public function addHtmlTag(HtmlTag $htmlTag): void
    {
        $this->allTags[] = $htmlTag;
    }

    /**
     * clear all controls
     * @return void
     */
    public function reset(): void
    {
        $this->allTags = [];
        $this->controlTags = [];
        $this->tagsWithForById = [];
    }

    /**
     * update all controls id and name entries, and any for values
     * @param string $newId
     * @param string $newName
     * @throws DeformException
     */
    public function changeNamespacedAttributes(string $newId, string $newName): void
    {
        $multipleControlTags = count($this->controlTags) > 1;
        foreach ($this->controlTags as $control) {
            $oldId = $control->get('id');
            if ($multipleControlTags) {
                $value = $control->get('value');
                if (!$value) {
                    throw new DeformComponentException(
                        "When there are multiple control tags they must specify a value"
                    );
                }
                $setNewId = BaseComponent::getMultiControlId($newId, $value);
            } else {
                $setNewId = $newId;
            }
            $control->setIfExists('id', $setNewId);
            $control->setIfExists('name', $newName);
            if (isset($this->tagsWithForById[$oldId])) {
                foreach ($this->tagsWithForById[$oldId] as $htmlTag) {
                    $htmlTag->setIfExists('for', $setNewId);
                }
            }
        }
    }

    /**
     * set the value(s))
     * @param mixed $value
     * @throws DeformException
     */
    public function setValue(mixed $value): void
    {
        if (is_array($value)) {
            if (count($value) != count($this->controlTags)) {
                throw new DeformComponentException(
                    "The number of values provided does not match the number of controls"
                );
            }
            foreach ($this->controlTags as $controlTag) {
                $controlTag->value(array_shift($value));
            }
        } else {
            foreach ($this->controlTags as $controlTag) {
                $controlTag->value($value);
            }
        }
    }

    /**
     * get the controls as tags
     * @return HtmlTag[]
     */
    public function getControls(): array
    {
        return $this->controlTags;
    }

    /**
     * get all the tags
     * @return array
     */
    public function getHtmlTags(): array
    {
        return $this->allTags;
    }
}
