<?php

namespace Letsweb\Bundle\EnumIntDoctrineBundle\Twig\Extension;

use Letsweb\Bundle\EnumIntDoctrineBundle\DBAL\Types\AbstractEnumType;

/**
 * BaseEnumExtension
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 */
abstract class AbstractEnumIntExtension extends \Twig_Extension
{
    /**
     * @var AbstractEnumType[] $registeredEnumTypes Array of registered ENUM types
     */
    protected $registeredEnumTypes = [];

    /**
     * Constructor
     *
     * @param array $registeredTypes Array of registered ENUM types
     */
    public function __construct(array $registeredTypes)
    {
        foreach ($registeredTypes as $type => $details) {
            if (is_subclass_of($details['class'], '\Letsweb\Bundle\EnumIntDoctrineBundle\DBAL\Types\AbstractEnumIntType')) {
                $this->registeredEnumTypes[$type] = $details['class'];
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    abstract public function getName();
}
