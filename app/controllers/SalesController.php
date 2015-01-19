<?php
/**
 * There you are at the new controller
 */
class SalesController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
	    // get data from DemoModel.php
	    //$data = DemoModel::getData();
	    
	    // return the view with the data
	    return View::make('Sales.index', compact('isFormValidate'));
		//return View::make('Demo.address', compact('data'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function addressValidation(){
		$salesModel = new SalesModel();
		$addressValidation =  $salesModel->validateAddress();
		return $addressValidation;
	}
	public function store()
	{
		$input = Input::all();
		$salesModel = new SalesModel();
		$fieldsValidation =  $salesModel->validate($input);
		$isFieldsValidated = false;
		if($fieldsValidation->passes())
			$isFieldsValidated = true;
		else
			$isFieldsValidated = false;

		if($isFieldsValidated){
			$salesResponse = $salesModel->insertToZoho();
			$customer_number = "";
			$ref_number = "";
			if(isset($salesResponse['customer_number']) && $salesResponse['customer_number'])
				$customer_number = $salesResponse['customer_number'];
			if(isset($salesResponse['ref_number']) && $salesResponse['ref_number'])
				$ref_number = $salesResponse['ref_number'];
			$isAddressValidated = true;
			$correctAddress = true;
			return Redirect::to('/sales/store')->with('success',$isFieldsValidated)->with('correctAddress',$correctAddress)->with('ref_number',$ref_number)->with('customer_number',$customer_number);
		}
		else{
			$isAddressValidated = false;
			$correctAddress = false;
			$messages = $fieldsValidation->messages();
			return Redirect::to('/sales/store')->with('success',$isFieldsValidated)->with('messages',$messages)->with('correctAddress',$correctAddress)->withInput();
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}