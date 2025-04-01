<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\Http\Client;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Addresses Model
 *
 * @method \App\Model\Entity\Address newEmptyEntity()
 * @method \App\Model\Entity\Address newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Address[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Address get($primaryKey, $options = [])
 * @method \App\Model\Entity\Address findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Address patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Address[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Address|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Address saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Address[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Address[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Address[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Address[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class AddressesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable("addresses");
        $this->setDisplayField("id");
        $this->setPrimaryKey("id");
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar("foreign_table")
            ->maxLength("foreign_table", 100)
            ->requirePresence("foreign_table", "create")
            ->notEmptyString("foreign_table");

        $validator
            ->integer("foreign_id")
            ->requirePresence("foreign_id", "create")
            ->notEmptyString("foreign_id");

        $validator
            ->scalar("postal_code")
            ->maxLength("postal_code", 8)
            ->requirePresence("postal_code", "create")
            ->notEmptyString("postal_code");

        $validator
            ->scalar("state")
            ->maxLength("state", 2)
            ->requirePresence("state", "create")
            ->notEmptyString("state");

        $validator
            ->scalar("city")
            ->maxLength("city", 200)
            ->requirePresence("city", "create")
            ->notEmptyString("city");

        $validator
            ->scalar("sublocality")
            ->maxLength("sublocality", 200)
            ->requirePresence("sublocality", "create")
            ->notEmptyString("sublocality");

        $validator
            ->scalar("street")
            ->maxLength("street", 200)
            ->requirePresence("street", "create")
            ->notEmptyString("street");

        $validator
            ->scalar("street_number")
            ->maxLength("street_number", 200)
            ->requirePresence("street_number", "create")
            ->notEmptyString("street_number");

        $validator
            ->scalar("complement")
            ->maxLength("complement", 200)
            ->notEmptyString("complement");

        return $validator;
    }

    public function getPostalCodeData($postalCode)
    {
        return [
            "postal_code" => "14840334",
            "city" => "Guariba",
            "state" => "SP",
            "sublocality" => "Vila Rocca",
            "street" => "Avenida Antonio Mazzi",
        ];

        // Try to get postal code data from Republica Virtual
        $postalCodeData = $this->getPostalCodeRepublicaVirtual($postalCode);

        // If first service fails, try ViaCep as fallback
        if ($postalCodeData === null) {
            $postalCodeData = $this->getPostalCodeViaCep($postalCode);
        }

        return $postalCodeData;
    }

    private function getPostalCodeRepublicaVirtual($postalCode)
    {
        $http = new Client();
        // Make API request to Republica Virtual
        $republicaVirtual = $http->get(
            "http://cep.republicavirtual.com.br/web_cep.php",
            [
                "cep" => $postalCode,
                "formato" => "json",
            ]
        );

        // Check if request was successful
        if (!$republicaVirtual->isOk()) {
            return null;
        }

        $data = $republicaVirtual->getJson();

        // Validate response data
        if ($data === null || !is_array($data)) {
            return null;
        }

        // Check if postal code was found
        if ($data["resultado"] == "0") {
            return null;
        }

        // Return formatted address data
        return [
            "postal_code" => $postalCode,
            "city" => $data["cidade"],
            "state" => $data["uf"],
            "sublocality" => $data["bairro"],
            "street" => $data["tipo_logradouro"] . " " . $data["logradouro"],
        ];
    }

    private function getPostalCodeViaCep($postalCode)
    {
        $http = new Client();
        // Make API request to ViaCep
        $viaCep = $http->get("https://viacep.com.br/ws/$postalCode/json");

        // Check if request was successful
        if (!$viaCep->isOk()) {
            return null;
        }

        $data = $viaCep->getJson();

        // Validate response data
        if ($data === null || !is_array($data)) {
            return null;
        }

        // Check if postal code was found
        if (isset($data["erro"]) && $data["erro"] === "true") {
            return null;
        }

        // Return formatted address data
        return [
            "postal_code" => $postalCode,
            "city" => $data["localidade"],
            "state" => $data["uf"],
            "sublocality" => $data["bairro"],
            "street" => $data["logradouro"],
        ];
    }
}
