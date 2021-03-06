<?php
namespace PSB\Core\Persistence;


use PSB\Core\Exception\UnexpectedValueException;

class StorageType
{
    const OUTBOX = 'Outbox';

    static protected $constants = ['OUTBOX' => self::OUTBOX];

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     */
    public function __construct($value)
    {
        if (!in_array($value, static::$constants)) {
            throw new UnexpectedValueException("'$value' is not one of the possible values for a StorageType.");
        }

        $this->value = $value;
    }

    /**
     * @return StorageType
     */
    public static function OUTBOX()
    {
        return new self(static::OUTBOX);
    }

    /**
     * @return array
     */
    public static function getConstants()
    {
        return static::$constants;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param StorageType $other
     *
     * @return bool
     */
    public function equals(StorageType $other)
    {
        return $this->value === $other->getValue();
    }
}
