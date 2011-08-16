<?php
namespace Everyman\Neo4j;

/**
 * A set of operations expected to succeed (or fail) atomically
 */
class Batch
{
    protected $client = null;

	protected $operations = array();

	/**
	 * Build the batch and set its client
	 *
	 * @param Client $client
	 */
	public function __construct(Client $client)
	{
		$this->client = $client;
	}

	/**
	 * Add an entity to the batch to delete
	 *
	 * @param PropertyContainer $entity
	 * @return integer
	 */
	public function delete(PropertyContainer $entity)
	{
		return $this->addOperation('delete', $entity);
	}

	/**
	 * Get the batch's client
	 *
	 * @return Client
	 */
	public function getClient()
	{
		return $this->client;
	}

	/**
	 * Add an entity to the batch to save
	 *
	 * @param PropertyContainer $entity
	 * @return integer
	 */
	public function save(PropertyContainer $entity)
	{
		return $this->addOperation('save', $entity);
	}
	
	/**
	 * Add an operation to the batch
	 *
	 * @param string $operation
	 * @param PropertyContainer $entity
	 * @return integer operation index
	 */
	protected function addOperation($operation, PropertyContainer $entity)
	{
		$opId = $this->checkOperation($operation, $entity);
		if ($opId === null) {
			$opId = count($this->operations);
			$this->operations[] = array(
				'operation' => $operation,
				'entity' => $entity,
			);
		}
	
		return $opId;
	}
	
	/**
	 * Check to see if the given operation is already being performed on the given entity
	 *
	 * @param string $operation
	 * @param PropertyContainer $entity
	 * @return integer operation index if operation is found
	 */
	protected function checkOperation($operation, PropertyContainer $entity)
	{
		foreach ($this->operations as $i => $op) {
			if ($op['operation'] == $operation && $op['entity'] === $entity) {
				return $i;
			}
		}
		return null;
	}
}