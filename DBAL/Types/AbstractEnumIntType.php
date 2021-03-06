<?php

namespace Letsweb\Bundle\EnumIntDoctrineBundle\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\PostgreSqlPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Types\Type;

/**
 * AbstractEnumType
 *
 * Provides support of MySQL ENUM type for Doctrine in Symfony applications
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 * @author Ben Davies <ben.davies@gmail.com>
 */
abstract class AbstractEnumIntType extends Type
{
    /**
     * @var string $name Name of this type
     */
    protected $name = '';

    /**
     * @var array $choices Array of ENUM Values, where ENUM values are keys and their readable versions are values
     * @static
     */
    protected static $choices = [];

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return (int) $value;
    }
    
    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        if (!in_array($value, $this->getValues())) {
            throw new \InvalidArgumentException(sprintf('Invalid value "%s" for ENUMINT %s.', $value, $this->getName()));
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'TINYINT(1)';
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name ?: (new \ReflectionClass(get_class($this)))->getShortName();
    }

    /**
     * Get readable choices for the ENUM field
     *
     * @static
     *
     * @return array Values for the ENUM field
     */
    public static function getChoices()
    {
        return static::$choices;
    }

    /**
     * Get values for the ENUM field
     *
     * @static
     *
     * @return array Values for the ENUM field
     */
    public static function getValues()
    {
        return array_keys(static::getChoices());
    }

    /**
     * Get value in readable format
     *
     * @param string $value ENUM value
     *
     * @static
     *
     * @return string|null $value Value in readable format
     *
     * @throws \InvalidArgumentException
     */
    public static function getReadableValue($value)
    {
        if (!isset(static::getChoices()[$value])) {
            $message = sprintf('Invalid value "%s" for ENUM type "%s".', $value, get_called_class());

            throw new \InvalidArgumentException($message);
        }

        return static::getChoices()[$value];
    }

    /**
     * Check if some string value exists in the array of ENUM values
     *
     * @param string $value ENUM value
     *
     * @return bool
     */
    public static function isValueExist($value)
    {
        return in_array($value, static::getValues());
    }
}
