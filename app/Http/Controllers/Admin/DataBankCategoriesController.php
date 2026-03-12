<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyDataBankCategoryRequest;
use App\Http\Requests\StoreDataBankCategoryRequest;
use App\Http\Requests\UpdateDataBankCategoryRequest;
use App\Models\DataBankCategory;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DataBankCategoriesController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('data_bank_category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $dataBankCategories = DataBankCategory::all();

        return view('admin.dataBankCategories.index', compact('dataBankCategories'));
    }

    public function create()
    {
        abort_if(Gate::denies('data_bank_category_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.dataBankCategories.create');
    }

    public function store(StoreDataBankCategoryRequest $request)
    {
        $dataBankCategory = DataBankCategory::create($request->all());

        return redirect()->route('admin.data-bank-categories.index');
    }

    public function edit(DataBankCategory $dataBankCategory)
    {
        abort_if(Gate::denies('data_bank_category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.dataBankCategories.edit', compact('dataBankCategory'));
    }

    public function update(UpdateDataBankCategoryRequest $request, DataBankCategory $dataBankCategory)
    {
        $dataBankCategory->update($request->all());

        return redirect()->route('admin.data-bank-categories.index');
    }

    public function show(DataBankCategory $dataBankCategory)
    {
        abort_if(Gate::denies('data_bank_category_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $dataBankCategory->load('dataBankCategoryDataBanks');

        return view('admin.dataBankCategories.show', compact('dataBankCategory'));
    }

    public function destroy(DataBankCategory $dataBankCategory)
    {
        abort_if(Gate::denies('data_bank_category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $dataBankCategory->delete();

        return back();
    }

    public function massDestroy(MassDestroyDataBankCategoryRequest $request)
    {
        $dataBankCategories = DataBankCategory::find(request('ids'));

        foreach ($dataBankCategories as $dataBankCategory) {
            $dataBankCategory->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
