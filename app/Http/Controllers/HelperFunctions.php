<?php

namespace App\Http\Controllers;

class HelperFunctions extends Controller{


    public static function generateCases($attributes) {
        $cases = [[]];
        foreach ($attributes as $attributeValues) {
          $newCases = [];
          foreach ($cases as $case) {
            foreach ($attributeValues as $value) {
              $newCases[] = array_merge($case, [$value]);
            }
          }
          $cases = $newCases;
        }
        return $cases;
      }
    
    public static function filterExisting($filterData, $dataIDS ){
        $dataExisting = $filterData != null ? count($filterData) == 0 ? true : false : true;
        if($dataExisting == false and array_intersect($filterData, $dataIDS->toArray()) == $filterData){
            return true;
        }
        return $dataExisting ;
    }

    
    public static function filterColumns($filters, $columns){
        $filters['search'] = isset($filters['search']) ? $filters['search'] : null;
        foreach($columns as $column){
            $filters['filters'][$column] = isset($filters['filters'][$column]) ? $filters['filters'][$column] : null;
        }
        $filters['startDate'] = isset($filters['startDate']) ? $filters['startDate'] : null;
        $filters['endDate'] = isset($filters['endDate']) ? $filters['endDate'] : null;
        $filters['pagination']['current_page'] = isset($filters['pagination']['current_page']) ? $filters['pagination']['current_page'] : null;
        $filters['pagination']['per_page'] = isset($filters['pagination']['per_page']) ? $filters['pagination']['per_page'] : null;
        return $filters;
    }

    public static function getPagination($data, $per_page, $current_page){
        $total_rows = $data->count();
        $per_page = ($per_page == null or $per_page== 0) ? 10 : $per_page;
        $pages = ceil($total_rows/$per_page) != 0 ? range(0, ceil($total_rows/$per_page)-1) : [0];
        $current_page = ($current_page == null or $current_page> end($pages))  ? 1 : $current_page +1;
        $data = $data->forpage($current_page, $per_page)->values()->toArray();
        return [
            'statut'=>1,
            'data' => $data,
            'per_page' => $per_page,
            'current_page' => $current_page,
            'total'=>$total_rows
        ];
    }
    public static function getInactiveData($all_data, $active_data){
      $allData = collect($all_data);
      $activeData = collect($active_data);
      
      $filteredData = $allData->reject(function ($data) use ($activeData) {
          return $activeData->contains($data);
      });
      return $filteredData;
    }
}
