<?php

namespace Letsweb\Bundle\EnumIntDoctrineBundle\Grid;

use Oro\Bundle\DataGridBundle\Datagrid\AbstractColumnOptionsGuesser;
use Oro\Bundle\DataGridBundle\Extension\Formatter\Property\PropertyInterface as Property;
use Oro\Bundle\DataGridBundle\Datagrid\Guess\ColumnGuess;

class EnumIntColumnOptionsGuesser extends AbstractColumnOptionsGuesser
{
    public function guessFormatter($class, $property, $type)
    {
        switch ($type) {
            case 'payment_mode':
                $options = [
                    'frontend_type' => Property::TYPE_STRING
                ];
                break;
        }
        return isset($options)
            ? new ColumnGuess($options, ColumnGuess::MEDIUM_CONFIDENCE)
            : null;
    }
}