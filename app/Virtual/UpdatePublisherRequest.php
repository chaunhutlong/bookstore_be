<?php

/**
 * @OA\Schema(
 *      title="Update Publisher request",
 *      description="Update Publisher request body data",
 *      type="object"
 * )
 */

class UpdatePublisherRequest
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
    public $description;
}
