<?php
/**
 * Created by PhpStorm.
 * User: GitzJoey
 * Date: 9/7/2016
 * Time: 12:34 AM
 */

namespace App\Http\Controllers;

use App\Supplier;
use App\Pic;
use App\Profile;
use App\PhoneNumber;
use App\PhoneProvider;
use App\Product;
use App\Bank;
use App\BankAccount;
use App\SupplierSetting;
use App\Lookup;

use URL;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

	public function index()
	{
        $suppliers = Supplier::paginate(10);;
	    return view('suppliers.index', compact('suppliers'));
	}

	public function show($id)
	{
        $supplier = Supplier::findOrFail($id);

        return view('suppliers.show', compact('supplier'));
	}

	public function create()
	{
        $statusDDL = Lookup::where('category', '=', 'STATUS')->get()->pluck('description', 'code');
        return view('suppliers.create', compact('statusDDL'));
	}

	public function store(Request $request)
	{
        $this->validate($request,[
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'fax_num' => 'string|max:255',
            'tax_id' => 'required|string|max:255',
            'status' => 'required',
        ]);

        $data = [
            'supplier_name' => $request->input('name'),
            'supplier_address' => $request->input('address'),
            'supplier_city' => $request->input('city'),
            'phone_number' => $request->input('phone_number'),
            'fax_num' => $request->input('fax_num'),
            'tax_id' => $request->input('tax_id'),
            'status' => $request->input('status'),
            'remarks' => $request->input('remarks'),
        ];

        Supplier::create($data);

        return redirect(route('db.master.supplier'));
	}

	public function edit($id)
	{
        $supplier = Supplier::findOrFail($id);
        $products = $supplier->products;
        $pics = $supplier->pic;
        $phones = Pic::filter($id)->get();
        $phone_provider = PhoneProvider::all();
        $banks = Bank::all();
        $bank_account = $supplier->bank;
        $statusDDL = Lookup::where('category', '=', 'STATUS')->get()->pluck('description', 'code');

        return view('suppliers.edit', compact('supplier', 'phone_provider','pics','products','banks','bank_account','statusDDL','phones'));
	}

	public function update(Request $request, $id)
	{
        $supplier = Supplier::findOrFail($id);

        if ($supplier) {
            $supplier->supplier_name = $request->input('name');
            $supplier->supplier_address = $request->input('address');
            $supplier->supplier_city = $request->input('city');
            $supplier->phone_number = $request->input('phone_number');
            $supplier->fax_num = $request->input('fax_num');
            $supplier->tax_id = $request->input('tax_id');
            $supplier->status = $request->input('status');
            $supplier->remarks = $request->input('remarks');
            $supplier->update();
            return redirect(route('db.master.supplier'));
        }
	}

	public function delete($id)
	{
        $supplier = Supplier::findOrFail($id);

        $supplier->delete();
        return redirect(route('db.master.supplier'));
	}

    public function storePic(Request $request, $id)
    {
        $this->validate($request,[
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'address' => 'required|string',
        ]);

        $data = [
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'address' => $request->input('address'),
        ];

        $profile = Profile::create($data);
        $ic = Profile::find($profile->id);
                //To add IC num
                $ic->ic_num = $profile->id;
                $ic->update();
        $supplier = Supplier::find($id);
        $supplier->pic()->attach($profile->id);
        return redirect('dashboard/master/supplier/edit/'.$id);
    }

    public function editPic($id, $pic_id)
    {
        $pic = Profile::find($pic_id);
        // return $pic;
        return view('suppliers.pic.edit', compact('pic','id','pic_id'));
    }

    public function updatePic(Request $request, $id, $pic_id)
    {
        $profile = Profile::findOrFail($pic_id);

        if ($profile) {
            $profile->first_name = $request->input('first_name');
            $profile->last_name = $request->input('last_name');
            $update = $profile->update();
            $ic = Profile::find($update->id);
                //To update IC num
                $ic->ic_num = $update->id;
                $ic->update();
            $supplier = Supplier::find($id);
            $supplier->pic()->sync($update->id);

            return 'success';
        }
    }

    public function addBank(request $request)
    {
        $this->validate($request,[
            'bank_id' => 'required',
            'account' => 'required|string|max:255',
            'status' => 'required',
        ]);
        $data = [
            'bank_id' => $request->input('bank_id'),
            'account_number' => $request->input('account'),
            'remarks' => $request->input('remarks'),
            'status' => $request->input('status'),
        ];

        $supplier_id = $request->input('supplier_id');
        $bank = BankAccount::create($data);
        $bank->supplier()->attach($supplier_id);
        return redirect(URL::previous());
    }

    public function editBank($id) {
        $bank_account = BankAccount::find($id);
        $banks = Bank::all();
        $statusDDL = Lookup::where('category', '=', 'STATUS')->get()->pluck('description', 'code');

        return view('suppliers.bank.edit', compact('bank_account', 'banks', 'statusDDL'));
    }

    public function updateBank(Request $request, $id) {
        $this->validate($request,[
            'bank_id' => 'required',
            'account' => 'required|string|max:255',
            'status' => 'required',
        ]);

        $bank_account = BankAccount::findOrFail($id);

        if ($bank_account) {
            $bank_account->bank_id = $request->input('bank_id');
            $bank_account->account = $request->input('account');
            $bank_account->remarks = $request->input('remarks');
            $bank_account->status = $request->input('status');
            $bank_account->update();
        }

        return redirect(URL::previous());
    }

    public function deleteBank($id)
    {
        $bank = BankAccount::findOrFail($id);
        $bank->delete();
        return redirect(URL::previous());
    }

    public function addSetting(Request $request)
    {
        $this->validate($request,[
            'due_day' => 'required',
        ]);

        $data = [
            'supplier_id' => $request->input('supplier_id'),
            'due_day' => $request->input('due_day'),
        ];

        SupplierSetting::create($data);

        return redirect(URL::previous());
    }
}