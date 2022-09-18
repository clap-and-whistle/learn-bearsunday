<?php

declare(strict_types=1);

namespace Cw\LearnBear\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class CheckAuth
{
}
