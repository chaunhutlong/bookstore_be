<?php

/**
 * @OA\Schema(
 *    title="Create or Update Review request",
 *   description="Create or Update Review request body data",
 *   type="object",
 *  required={"rating"}
 * )
 */
class CreateOrUpdateReviewRequest
{
    /**
     * @OA\Property(
     *      property="rating",
     *     type="float",
     *     description="Rating of the book",
     *    example="4.5"
     * )
     * @var float
     */
    public $rating;

    /**
     * @OA\Property(
     *    property="comment",
     *   type="string",
     *  description="Comment of the book",
     * example="This is a comment"
     * )
     * @var string
     */
}