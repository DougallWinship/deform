<?php

declare(strict_types=1);

namespace Deform\Component;

use Deform\Exception\DeformException;
use Deform\Html\Html;
use Deform\Html\HtmlTag;
use Deform\Util\Strings;

/**
 * @persistAttribute javascriptSelectFunction
 */
class Image extends File
{
    use Shadow\Image;

    // phpcs:disable
    private const PLACEHOLDER_IMAGE_BASE64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAP8AAADGCAMAAAAqo6adAAAAKlBMVEXQ0NDv7+/a2trd3d3X19fV1dXR0dHu7u7p6enj4+Pr6+vm5ubf39/z8/Ne8nUWAAADnElEQVR4nO2dC3qCQAyECUJVhPtft5Vt+wHuE/aZzH8BMpMJYhfTrgMAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAYhiHoR/Gr9JllGF43omWhYhevTwL5vtb+S8LPWU5MG7VK26la8rI7Sj+nYFX6aqyMX80f+VRuq5M3PTypRgwGNT/jMCzdG05uBv10zKULi49s1k+0VS6uuR8Wdr/Q1+6vtQYb35CboEvq3xaSteXGEf82Q/AYI8/EfOPwN4hn5g/Beue/HcwvwFaP/3fMH8CcOafef+d9z/m8z8Kv/93k10++29AT0f/S9eXmt4+AMzHv3MNAPf4OwLAv/1d97DoH0sXlwHzV8CF+Ze/X0aTfO6f/X/o/wIsRv77+Eujfy5dVU6Oj0HLJOHWt2F8LctGvYw7357+NU33+zQ9ZmG93/Al69wfAAAAAAAAAAAAAERB7t/TVgYhB0kGBjEnaVrU6ZJYAwZRZ6kf/L9gJ9OA7dGqQAN2J8vyEnA4WJdmwOd7BaIM0LxWISkB2rdK5Bhg+lmlEAOMvyqVkQDzj2pFGGCRL2EE7PLZJ8Ahn7sBTvm8R8BHPuMEeMnna4CnfK4j4C+fZQIC5HM0IEg+vxEIlV9/AoIOboLlV2/ALaS+E/Kp7hG4LQENOie/5gSsq2F86zspv2ID/lZDeNV3Wr7vBbLzvxjIp0FX5NeZgM1eJHd9l+RXacB+L4qjvovy3RfIzmErlr1B1+XXloCPpWC2+iLIr8wA3VIgY31R5NsukB3tSjhTg2LJrycBho14+vqiya/GAPNGLE19EeXrL5Adyz7EzwbFlV9DAqzrII/1RZZfgQGudXC7+qLLP14gO45loPsGpZBfNgFO+dv6ksgvaoBzF+JKn1Q+lRsBj+6/UQ1KJ7+UAZ7ylQEp5ZcZAb/wK/qk8qlEAry7v+JaGnyZ3AaEyU9P5hEw/AOIkuQ0oLbur+QzoEr5+UYg5M6flTwG1Nn9lRwGVCw/xwhUG35FagNq7v5KWgOql592BCoPvyKdAfV3fyWVARU+9GpJNAKtyKc0CWgk/Ir4BjQlP/4INBR+RVwD2ur+SkwDmus+RR2BFuVTvAQ0GH5FHAOalR9nBBoNv+K6Ae12f+WqAY3LvzoCrcunawlgIP+KASzknx8BHvLpbALYyD9nQNOf+wdOjAAn+RSeAEbhV4QZwKz7FDgC/ORTSALmhSWl3xUGAAAAAAAAAAAAACX4BlOgPUihNS/BAAAAAElFTkSuQmCC';
    // phpcs:enable

    /** @var null|HtmlTag */
    private ?HtmlTag $previewImageTag = null;

    /** @var string|null */
    private ?string $javascriptSelectFunction = null;

    /** @var HtmlTag|null  */
    private ?HtmlTag $hiddenUrlInput = null;

    /**
     * @return void
     * @throws DeformException
     */
    public function setup(): void
    {
        parent::setup();
        $this->accept("image/*");
    }

    /**
     * @return HtmlTag
     * @throws DeformException
     */
    public function getHtmlTag(): HtmlTag
    {
        $this->input->set(
            'onchange',
            'if (0 in this.files)'
            . ' { this.nextSibling.src = window.URL.createObjectURL(this.files[0]); }'
            . ' this.nextSibling.nextSibling.value="";'
        );
        $htmlTag = parent::getHtmlTag();
        list($labelDiv, $componentDiv) = $htmlTag->getChildren();
        if (!$this->previewImageTag) {
            $this->addSupportTags();
        }
        $this->input->css('display', 'none');
        $onclickJs = 'let input = this.parentNode.nextSibling.firstChild; '
            . 'input.nextSibling.src = "' . self::PLACEHOLDER_IMAGE_BASE64 . '";'
            . 'input.value=null;'
            . 'input.nextSibling.nextSibling.value=null';
        $this->clearButton->set('onclick', $onclickJs);
        $componentDiv->add($this->previewImageTag);
        $componentDiv->add($this->hiddenUrlInput);
        return $htmlTag;
    }

    /**
     * @param mixed $value
     * @return static
     * @throws DeformException
     */
    public function setValue(mixed $value): static
    {
        if (!$this->previewImageTag) {
            $this->addSupportTags($value ?: '');
        } else {
            $this->previewImageTag->set('src', $value);
            $this->hiddenUrlInput->set('value', $value);
        }
        return parent::setValue($value);
    }

    /**
     * @param string $src
     * @throws DeformException
     */
    private function addSupportTags(string $src = ''): void
    {
        $this->hiddenUrlInput = Html::input([
            'id' => 'hidden-' . $this->getId(),
            'type' => 'hidden',
            'name' => $this->input->get('name'),
            'value' => $src
        ]);
        if (!$src) {
            $src = self::PLACEHOLDER_IMAGE_BASE64;
        }
        $this->previewImageTag = Html::img([
            'id' => 'preview-' . $this->getId(),
            'src' => $src,
            'alt' => '',
            'style' => 'max-width:200px;max-height:200px;cursor:pointer',
            'onclick' => $this->javascriptSelectFunction ?:
                'this.previousSibling.dispatchEvent('
                . 'new MouseEvent("click",{bubbles: false,cancelable: true,view: window})'
                . ');'
        ]);
    }

    /**
     * specify a function to determine what happens when the preview image is clicked
     * such a function should return a Promise which resolves with the url of an image, for example
     * ```JS
     * const selectImage = (evt) => {
     *     return new Promise(function(resolve,reject) {
     * };
     * ```
     * and then
     * ```PHP
     * $imageComponent->setJavascriptSelectFunction('selectImage');
     * ```
     * the default behaviour will just open the component's hidden input
     *
     * @param string $js
     * @return static
     * @throws DeformException
     */
    public function setJavascriptSelectFunction(string $js): static
    {
        $id = $this->getId();
        $previewId = 'preview-' . $id;
        $hiddenId = 'hidden-' . $id;

        $javascriptSelectFunction = <<<JS
if (typeof $js!=="function") { 
    alert("'$js' is not a valid javascript function");
} else { 
    $js(event).then(function(url) { 
        if (url) { 
            document.getElementById("$previewId").src=url; 
            document.getElementById("$hiddenId").value=url 
        } 
    }, function(error) {
        console.error("Failed to use preview", error); 
    }) 
}
JS;
        $this->javascriptSelectFunction = Strings::trimInternal($javascriptSelectFunction);
        return $this;
    }
}
