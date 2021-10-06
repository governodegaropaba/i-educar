<?php

class MunicipioController extends ApiCoreController
{

    // search options
    protected function searchOptions()
    {
        return ['namespace' => 'public', 'idAttr' => 'idmun', 'selectFields' => ['sigla_uf']];
    }

    // subscreve formatResourceValue para adicionar a sigla do estado ao final do valor,
    // "<id_municipio> - <nome_municipio> (<sigla_uf>)", ex: "1 - Içara (SC)"
    protected function formatResourceValue($resource)
    {
        $siglaUf = $resource['sigla_uf'];
        $nome = $this->toUtf8($resource['name'], ['transform' => true]);

        return $resource['id'] . " - $nome ($siglaUf)";
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'municipio-search')) {
            $this->appendResponse($this->search());
        } else {
            $this->notImplementedOperationError();
        }
    }

    protected function search()
    {
        if ($this->canSearch()) {
            if (is_numeric($this->getRequest()->query)){
                $where = 'AND idmun = :idmun';
                $field = 'idmun';
            } else {
                $where = "AND LOWER(UNACCENT(nome)) LIKE '%' || LOWER(UNACCENT(:nome)) || '%'";
                $field = 'nome';
            }
            $sql = "
                SELECT
                    DISTINCT sigla_uf,
                    idmun AS id,
                    nome AS name,
                    LENGTH(nome) AS size
                FROM
                    public.municipio
                WHERE TRUE
                    {$where}
                ORDER BY
                    size,
                    nome
                LIMIT 15";

            $tmpResults = $this->fetchPreparedQuery($sql, [$field => trim($this->getRequest()->query)], false);
            $results = [];

            foreach ($tmpResults as $result) {
                if (!isset($results[$result['id']])) {
                    $results[$result['id']] = $this->formatResourceValue($result);
                }
            }

            $resources = $results;
        }

        if (empty($resources)) {
            $resources = ['' => 'Sem resultados.'];
        }

        return ['result' => $resources];
    }
}
