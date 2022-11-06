<?php

/**
 * @OA\Schema(
 *      title="Store Discount request",
 *      description="Store Discount request body data",
 *      type="object",
 *      required={"name, value"}
 * )
 */

class StoreDiscountRequest
{
    /**
     * @OA\Property(
     *      title="name",
     *      description="Name of the new discount",
     *      example="A nice discount"
     * )
     *
     * @var string
     */
    public $name;

    /**
     * @OA\Property(
     *      title="value",
     *      description="Value of the new discount",
     *      example="10"
     * )
     * @var float
     */
    public $value;

    /**
     * @OA\Property(
     *      title="start_date",
     *      description="Start date of the new discount",
     *      example="2020-01-27 17:50:45"
     * )
     * @var \DateTime
     */
    public $start_date;

    /**
     * @OA\Property(
     *      title="end_date",
     *      description="End date of the new discount",
     *      example="2020-01-27 17:50:45"
     * )
     * @var \DateTime
     */
    public $end_date;

    /**
     * @OA\Property(
     *      title="description",
     *     description="Description of the new discount",
     *    example="A nice description"
     * )
     * @var string
     */
    public $description;
}
