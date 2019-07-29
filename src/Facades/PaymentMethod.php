<?php
namespace LifeOnScreen\LaravelQuickBooks\Facades;

use QuickBooksOnline\API\Facades\FacadeHelper;

/**
 * Provisional Facade while is incorporated in QuickBooksOnline Package
 * Class PaymentMethod
 * @package LifeOnScreen\LaravelQuickBooks\Facades
 */
class PaymentMethod{

    public static function create(array $data, $throwException = TRUE){
        if(!isset($data) || empty($data)) throw new \Exception("Passed array for creating PaymentMethod is Empty");
        $PaymentObject = FacadeHelper::reflectArrayToObject("PaymentMethod", $data, $throwException );
        return $PaymentObject;
    }

    /**
     * This is an immutable function
     */
    public static function update($objToUpdate, array $data){
        $classOfObj = get_class($objToUpdate);
        if(strcmp($classOfObj, FacadeHelper::simpleAppendClassNameSpace("PaymentMethod")) != 0){
            throw new \Exception("Target object class:{" .  $classOfObj . "} is not an instance of PaymentMethod.");
        }
        $newPaymentObj = Payment::create($data);
        $clonedOfObj = FacadeHelper::cloneObj($objToUpdate);
        FacadeHelper::mergeObj($clonedOfObj, $newPaymentObj);
        return $clonedOfObj;
    }

}