<?php

/**
 * @OA\Schema(
 * title="ReviewResource",
 * description="Review resource",
 * @OA\Xml(
 * name="ReviewResource"
 * )
 * )
 */
class ReviewResource
{
    /**
     * @OA\Property(
     * title="Data",
     * description="Data wrapper"
     * )
     *
     * @var \App\Virtual\Models\Review[]
     */
    public $Review;
}