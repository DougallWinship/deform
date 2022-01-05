<?php
namespace Deform\Html;

/**
 * convenient select tag generation
 */
class Select extends HtmlTag
{
    private static $nonMultiSelectedChosen;

    /**
     * @param array $options array specifying the select options
     * @param array $attributes attributes for the select tag itself
     * @param bool $truncateSize whether and at what length to truncate option text
     * @param bool $hasOptgroups if yes then the options array should be 1 level deeper with shallowest level being the optgroup labels
     *
     * @return HtmlTag|HtmlTag
     * @throws \Exception
     */
    public static function generateSelectWithOptions(
        array $options,
        array $attributes = [],
        bool $truncateSize = false,
        bool $hasOptgroups = false): HtmlTag
    {
        $selected_id = false;
        if(isset($attributes["selected_id"])) {
            $selected_id = $attributes["selected_id"];
            unset($attributes["selected_id"]);
        }
        if(isset($attributes["disabled"]) && !$attributes["disabled"]) {
            // be sure not to include disabled='' or disabled=false as this will disable the select!
            unset($attributes["disabled"]);
        }
        if(isset($attributes["multiple"]) && !$attributes["multiple"]) {
            // don't include multiple='' or multiple=false as this will enable multiple!
            unset($attributes["multiple"]);
        }
        self::$nonMultiSelectedChosen = false;// ensure this is always reset
        $is_multi_select = isset($attributes["multiple"]);

        $select_tag = Html::select($attributes);
        if($hasOptgroups) {
            foreach($options as $optgroup_label => $optgroup_options) {
                $opt_group = Html::optgroup()->label($optgroup_label);
                foreach($optgroup_options as $option_key => $option_value) {
                    $opt_group->add(self::generateOption($option_key, $option_value, $truncateSize, $selected_id, $is_multi_select));
                }
                $select_tag->add($opt_group);
            }
        } else {
            foreach($options as $option_key => $option_value) {
                $select_tag->add(self::generateOption($option_key, $option_value, $truncateSize, $selected_id, $is_multi_select));
            }
        }
        return $select_tag;
    }

    /**
     * attempting to retrofit old select generation code
     *
     * @param string $optionKey
     * @param array|string|object $optionValue
     * @param int|null $truncateSize
     * @param mixed $selectedId
     * @param bool $isMultiSelect
     *
     * @return HtmlTag
     * @throws \Exception
     *@deprecated
     *
     */
    public static function generateOption(
        string $optionKey,
        string $optionValue,
        int $truncateSize = null,
        string $selectedId = null,
        bool  $isMultiSelect = false): HtmlTag
    {
        $optionAttributes = [];
        if(is_object($optionValue)) {
            if(method_exists($optionValue, "getId")) {
                $optionAttributes["value"] = $optionValue->getId();
            }
            $text = (string)$optionValue;
        } else {
            if(is_array($optionValue)) {
                $optionAttributes["value"] = $optionKey;
                if(isset($optionValue["text"])) {
                    $text = $optionValue["text"];
                    unset($optionValue["text"]);
                } else {
                    $text = $optionKey;
                }
                $optionAttributes = array_merge($optionAttributes, $optionValue);
            } else {
                $optionAttributes["value"] = $optionKey;
                $text                       = $optionValue;
            }
        }
        if ($isMultiSelect && is_array($selectedId)) {
            if (in_array($optionAttributes["value"],$selectedId)) {
                $optionAttributes["selected"] = "selected";
            }
        }
        elseif($optionAttributes["value"] == $selectedId) {
            if($isMultiSelect) {
                $optionAttributes["selected"] = "selected";
            } elseif(!self::$nonMultiSelectedChosen) {
                $optionAttributes["selected"]   = "selected";
                self::$nonMultiSelectedChosen = true;
            }
        }
        if(isset($optionAttributes["disabled"])) {
            if($optionAttributes["disabled"]) {
                $optionAttributes["disabled"] = "disabled";
            } else {
                unset($optionAttributes["disabled"]);
            }
        }
        if($truncateSize && strlen($text) > $truncateSize) {
            $text = substr($text, 0, $truncateSize);
        }

        return Html::option($optionAttributes)->add($text);
    }

    /**
     * optionally add a "select from drop down" type message with a blank value
     *
     * @param string $text
     *
     * @throws \Exception
     */
    public function setSelectOptionText(string $text)
    {
        $this->prepend(Html::option(["value" => ""])->add($text));
    }
}
