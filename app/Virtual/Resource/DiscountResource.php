<?php

/**
 * @OA\Schema(
 * title="DiscountResource",
 * description="Discount resource",
 * @OA\Xml(
 * name="DiscountResource"
 * )
 * )
 */
class DiscountResource
{
  /**
   * @OA\Property(
   * title="Data",
   * description="Data wrapper"
   * )
   *
   * @var \App\Virtual\Models\Discount[]
   */
  public $discount;
}
