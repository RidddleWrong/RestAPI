<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreCustomerRequest;
use App\Http\Requests\V1\UpdateCustomerRequest;
use App\Models\Customer;
use App\Http\Resources\V1\CustomerResource;
use App\Http\Resources\V1\CustomerCollection;
use App\Filters\V1\CustomersFilter;
use Illuminate\Http\Request;
use App\Http\Requests\V1\BulkStoreInvoiceRequest;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = new CustomersFilter();
        $filterParms = $filter->transform($request); // [['column','operator','value']]

        $customers = Customer::where($filterParms);

        $includeInvoices = $request->query('includeInvoices');

        if($includeInvoices){
            $customers = $customers->with('invoices');
        }
        return new CustomerCollection($customers->paginate()->appends($request->query()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request)
    {
        return new CustomerResource(Customer::create($request->validated()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $includeInvoices = request()->query('includeInvoices');

        if($includeInvoices){
            $customer = $customer->loadMissing('invoices');
        }

        return new CustomerResource($customer);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer->update($request->all());
        return $customer;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();
    }
}
