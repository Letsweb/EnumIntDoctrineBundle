<?php

namespace Letsweb\Bundle\EnumIntDoctrineBundle\Form;

use Doctrine\Common\Persistence\ManagerRegistry;
use Letsweb\Bundle\EnumIntDoctrineBundle\DBAL\Types\AbstractEnumIntType;
use Letsweb\Bundle\EnumIntDoctrineBundle\Exception\EnumTypeIsRegisteredButClassDoesNotExistException;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmTypeGuesser;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;

/**
 * EnumTypeGuesser
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 */
class EnumIntTypeGuesser extends DoctrineOrmTypeGuesser
{
    /**
     * @var AbstractEnumIntType[] $registeredEnumTypes Array of registered ENUM types
     */
    protected $registeredEnumTypes = [];

    /**
     * Constructor
     *
     * @param ManagerRegistry $registry        Registry
     * @param array           $registeredTypes Array of registered ENUM types
     */
    public function __construct(ManagerRegistry $registry, array $registeredTypes)
    {
        parent::__construct($registry);

        foreach ($registeredTypes as $type => $details) {
            $this->registeredEnumTypes[$type] = $details['class'];
        }
    }

    /**
     * Returns a field guess for a property name of a class
     *
     * @param string $class    The fully qualified class name
     * @param string $property The name of the property to guess for
     *
     * @return TypeGuess A guess for the field's type and options
     *
     * @throws EnumTypeIsRegisteredButClassDoesNotExistException
     */
    public function guessType($class, $property)
    {
        $classMetadata = $this->getMetadata($class);

        // If no metadata for this class - can't guess anything
        if (!$classMetadata) {
            return null;
        }

        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $metadata */
        list($metadata) = $classMetadata;
        $fieldType = $metadata->getTypeOfField($property);
        // This is not one of the registered ENUM types
        if (!isset($this->registeredEnumTypes[$fieldType])) {
            return null;
        }

        $enumTypeFullClassName = $this->registeredEnumTypes[$fieldType];
        $abstractEnumTypeFullClassName = 'Letsweb\Bundle\EnumIntDoctrineBundle\DBAL\Types\AbstractEnumIntType';


        if (get_parent_class($enumTypeFullClassName) !== $abstractEnumTypeFullClassName) {
            return null;
        }

        if (!class_exists($enumTypeFullClassName)) {
            throw new EnumTypeIsRegisteredButClassDoesNotExistException(sprintf(
                'ENUM type "%s" is registered as "%s", but that class does not exist',
                $fieldType,
                $enumTypeFullClassName
            ));
        }



        // Get the choices from the fully qualified class name
        $parameters = [
            'choices'  => $enumTypeFullClassName::getChoices(),
            'required' => !$metadata->isNullable($property),
        ];

        return new TypeGuess('choice', $parameters, Guess::VERY_HIGH_CONFIDENCE);
    }
}
