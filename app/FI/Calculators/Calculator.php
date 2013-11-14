<?php namespace FI\Calculators;

abstract class Calculator {

	protected $id;
	protected $items = array();
	protected $taxRates = array();
	
	protected $calculatedItemAmounts = array();
	protected $calculatedTaxRates = array();
	protected $calculatedAmount = array();

	public function __construct()
	{
		$this->calculatedAmount = array(
			'item_subtotal'  => 0,
			'item_tax_total' => 0,
			'tax_total'      => 0,
			'total'          => 0
		);
	}

	/**
	 * Sets the id
	 * @param int $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * Adds a item for calculation
	 * @param int $itemId
	 * @param float $quantity       [description]
	 * @param float $price          [description]
	 * @param float $taxRatePercent [description]
	 */
	public function addItem($itemId, $quantity, $price, $taxRatePercent)
	{
		$this->items[] = array(
			'itemId'         => $itemId,
			'quantity'       => $quantity,
			'price'          => $price,
			'taxRatePercent' => $taxRatePercent
		);
	}

	/**
	 * Adds a tax rate for calculation
	 * @param int $taxRateId
	 * @param float $taxRatePercent
	 * @param int $includeItemTax
	 */
	public function addTaxRate($taxRateId, $taxRatePercent, $includeItemTax)
	{
		$this->taxRates[] = array(
			'taxRateId'      => $taxRateId,
			'taxRatePercent' => $taxRatePercent,
			'includeItemTax' => $includeItemTax
		);
	}

	/**
	 * Call the calculation methods
	 */
	public function calculate()
	{
		$this->calculateItems();
		$this->calculateTaxRates();
	}

	/**
	 * Returns calculated item amounts
	 * @return array
	 */
	public function getCalculatedItemAmounts()
	{
		return $this->calculatedItemAmounts;
	}

	/**
	 * Returns calculated tax rates
	 * @return array
	 */
	public function getCalculatedTaxRates()
	{
		return $this->calculatedTaxRates;
	}

	/**
	 * Returns overall calculated amount
	 * @return array
	 */
	public function getCalculatedAmount()
	{
		return $this->calculatedAmount;
	}

	/**
	 * Calculates the items
	 */
	protected function calculateItems()
	{
		foreach ($this->items as $item)
		{
			$subtotal = $item['quantity'] * $item['price'];

			if ($item['taxRatePercent'])
			{
				$taxTotal = $subtotal  * ($item['taxRatePercent'] / 100);
			}
			else
			{
				$taxTotal = 0;
			}

			$total = $subtotal + $taxTotal;

			$this->calculatedItemAmounts[] = array(
				'item_id'   => $item['itemId'],
				'subtotal'  => $subtotal,
				'tax_total' => $taxTotal,
				'total'     => $total
			);

			$this->calculatedAmount['item_subtotal']  += $subtotal;
			$this->calculatedAmount['item_tax_total'] += $taxTotal;
			$this->calculatedAmount['total']          += $total;
		}
	}

	/**
	 * Calculates the tax rates
	 */
	protected function calculateTaxRates()
	{
		foreach ($this->taxRates as $taxRate)
		{
			if (!$taxRate['includeItemTax'])
			{
				$taxTotal = $this->calculatedAmount['item_subtotal'] * ($taxRate['taxRatePercent'] / 100);

			}
			else
			{
				$taxTotal = $this->calculatedAmount['total'] * ($taxRate['taxRatePercent'] / 100);
			}

			$this->calculatedTaxRates[] = array(
				'id'               => $this->id,
				'tax_rate_id'      => $taxRate['taxRateId'],
				'include_item_tax' => $taxRate['includeItemTax'],
				'tax_total'        => $taxTotal
			);

			$this->calculatedAmount['tax_total'] += $taxTotal;
			$this->calculatedAmount['total']     += $taxTotal;
		}
	}

}