<?php
namespace Core\Domain\Adapter;

/**
 * Interface ValidatorInterface
 *
 * @package Core\Domain\Adapter
 */
interface ValidatorInterface
{
    /**
     * @param $entity
     * @param $constraint
     *
     * @return mixed
     */
    public function validate($entity, $constraint);

    /**
     * @return mixed
     */
    public function notNull();

    /**
     * @param array $options
     *
     * @return mixed
     */
    public function length($options = array());

    /**
     * @param array $options
     *
     * @return mixed
     */
    public function type($options = array());

    /**
     * @return mixed
     */
    public function dateTime();

    /**
     * @param array $value
     *
     * @return mixed
     */
    public function greaterThan($value);
}
