<?php

/**
 * @OA\Schema(
 *      title="Store Publisher request",
 *      description="Store Publisher request body data",
 *      type="object",
 *      required={"name, address, phone_number"}
 * )
 */

class StorePublisherRequest
{
    /**
     * @OA\Property(
     *      title="name",
     *      description="Name of the new publisher",
     *      example="A nice publisher"
     * )
     *
     * @var string
     */
    public $name;

    /**
     * @OA\Property(
     *      title="address",
     *     description="Address of the new publisher",
     *    example="A nice address"
     * )
     * @var string
     */
    public $address;

    /**
     * @OA\Property(
     *      title="phone_number",
     *     description="Phone number of the new publisher",
     *    example="0123456789"
     * )
     * @var string
     */
    public $phone_number;

    /**
     * @OA\Property(
     *      title="description",
     *     description="Description of the new publisher",
     *    example="A nice description"
     * )
     * @var string
     */
}
