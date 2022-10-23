<?php

/**
 * @OA\Schema(
 *      title="Update Publisher request",
 *      description="Update Publisher request body data",
 *      type="object",
 *      required={"name"}
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
}
