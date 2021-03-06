<?php

namespace JK\LaraChartie\DataTable;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use JK\LaraChartie\Contracts\DataTable as Contract;
use JK\LaraChartie\Contracts\Factory\ColumnsFactory;
use JK\LaraChartie\Contracts\Factory\RowsFactory;
use JK\LaraChartie\Contracts\Formatter;
use JK\LaraChartie\Contracts\Source;
use JK\LaraChartie\Formatters\LineChartFormatter;



class DataTable implements Contract, Arrayable
{

	/**
	 * @var Collection
	 */
	protected $columns;

	/**
	 * @var Collection
	 */
	protected $rows;

	/**
	 * @var RowsFactory
	 */
	protected $rowsFactory;

	/**
	 * @var ColumnsFactory
	 */
	protected $columnsFactory;

	/**
	 * @var Formatter
	 */
	protected $formatter;



	/**
	 * @param RowsFactory    $rowsFactory
	 * @param ColumnsFactory $columnsFactory
	 * @param Formatter      $formatter |null
	 */
	public function __construct(RowsFactory $rowsFactory, ColumnsFactory $columnsFactory, Formatter $formatter = null)
	{
		$this->rowsFactory = $rowsFactory;
		$this->columnsFactory = $columnsFactory;

		$this->columns = new Collection();
		$this->rows = new Collection();

		$this->formatter = $formatter ?: new LineChartFormatter; // TODO move to the config file
	}



	/**
	 * @param string $source
	 * @return $this
	 */
	public function source($source)
	{
		/** @var Source $source */
		$source = app($source);

		$source->columns($this);
		$source->fill($this);

		return $this;
	}



	/**
	 * @param Formatter $formatter
	 * @return $this
	 */
	public function formatter(Formatter $formatter)
	{
		$this->formatter = $formatter;

		return $this;
	}



	/**
	 * {@inheritdoc}
	 */
	public function addColumn($type, $label)
	{
		$this->columns[] = $this->columnsFactory->create($type, $label);

		return $this;
	}



	/**
	 * {@inheritdoc}
	 */
	public function addStringColumn($label)
	{
		return $this->addColumn(Type::STRING, $label);
	}



	/**
	 * {@inheritdoc}
	 */
	public function addNumberColumn($label)
	{
		return $this->addColumn(Type::NUMBER, $label);
	}



	/**
	 * {@inheritdoc}
	 */
	public function addBooleanColumn($label)
	{
		return $this->addColumn(Type::BOOLEAN, $label);
	}



	/**
	 * {@inheritdoc}
	 */
	public function addDateColumn($label)
	{
		return $this->addColumn(Type::DATE, $label);
	}



	/**
	 * {@inheritdoc}
	 */
	public function addDateTimeColumn($label)
	{
		return $this->addColumn(Type::DATETIME, $label);
	}



	/**
	 * @param array ...$values
	 * @return $this
	 */
	public function addRow(... $values)
	{
		$this->rows[] = $this->rowsFactory->create($values, count($this->columns));

		return $this;
	}



	/**
	 * @param array $rows
	 * @return $this
	 */
	public function addRows(array $rows)
	{
		foreach ($rows as $row) {
			$this->addRow($row);
		}

		return $this;
	}



	/**
	 * {@inheritdoc}
	 */
	public function columns()
	{
		return $this->columns;
	}



	/**
	 * {@inheritdoc}
	 */
	public function rows()
	{
		return $this->rows;
	}



	/**
	 * @return array
	 */
	public function toArray()
	{
		return $this->formatter->format($this);
	}
}
