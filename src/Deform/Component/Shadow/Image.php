<?php

declare(strict_types=1);

namespace Deform\Component\Shadow;

trait Image
{
    public function getShadowTemplate(): string
    {
        return parent::getShadowTemplate()."<style>.label-container label { float:left } .label-container button { float:right }</style>";
    }
}