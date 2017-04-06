<?php

namespace Letsweb\Bundle\EnumIntDoctrineBundle\Twig\Extension;

use Letsweb\Bundle\EnumIntDoctrineBundle\Exception\EnumTypeIsNotRegisteredException;
use Letsweb\Bundle\EnumIntDoctrineBundle\Exception\NoRegisteredEnumTypesException;
use Letsweb\Bundle\EnumIntDoctrineBundle\Exception\ConstantIsFoundInFewRegisteredEnumTypesException;
use Letsweb\Bundle\EnumIntDoctrineBundle\Exception\ConstantIsNotFoundInAnyRegisteredEnumTypeException;

/**
 * EnumConstantExtension
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 */
class EnumIntConstantExtension extends AbstractEnumIntExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [new \Twig_SimpleFilter('enum_constant', [$this, 'getEnumConstant'])];
    }

    /**
     * Get value of the ENUM constant
     *
     * @param string      $enumConstant ENUM constant
     * @param string|null $enumType     ENUM type
     *
     * @throws EnumTypeIsNotRegisteredException
     * @throws NoRegisteredEnumTypesException
     * @throws ConstantIsFoundInFewRegisteredEnumTypesException
     * @throws ConstantIsNotFoundInAnyRegisteredEnumTypeException
     *
     * @return string
     */
    public function getEnumConstant($enumConstant, $enumType = null)
    {
        if (!empty($this->registeredEnumTypes) && is_array($this->registeredEnumTypes)) {
            // If ENUM type was set, e.g. {{ 'CENTER'|enum_constant('BasketballPositionType') }}
            if (!empty($enumType)) {
                if (!isset($this->registeredEnumTypes[$enumType])) {
                    throw new EnumTypeIsNotRegisteredException(sprintf('ENUM type "%s" is not registered.', $enumType));
                }

                return constant($this->registeredEnumTypes[$enumType].'::'.$enumConstant);
            } else {
                // If ENUM type wasn't set, e.g. {{ 'CENTER'|enum_constant }}
                $occurrences = [];
                // Check if constant exists in registered ENUM types
                foreach ($this->registeredEnumTypes as $registeredEnumType) {
                    $reflection = new \ReflectionClass($registeredEnumType);
                    if ($reflection->hasConstant($enumConstant)) {
                        $occurrences[] = $registeredEnumType;
                    }
                }

                // If found only one occurrence, then we know exactly which ENUM type
                if (count($occurrences) == 1) {
                    $enumClassName = array_pop($occurrences);

                    return constant($enumClassName.'::'.$enumConstant);
                } elseif (count($occurrences) > 1) {
                    throw new ConstantIsFoundInFewRegisteredEnumTypesException(sprintf(
                        'Constant "%s" is found in few registered ENUM types. You should manually set the appropriate one.',
                        $enumConstant
                    ));
                } else {
                    throw new ConstantIsNotFoundInAnyRegisteredEnumTypeException(sprintf(
                        'Constant "%s" wasn\'t found in any registered ENUM type.',
                        $enumConstant
                    ));
                }
            }
        } else {
            throw new NoRegisteredEnumTypesException('There are no registered ENUM types.');
        }
    }

}
