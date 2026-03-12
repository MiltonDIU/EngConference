<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyDataBankRequest;
use App\Http\Requests\StoreDataBankRequest;
use App\Http\Requests\UpdateDataBankRequest;
use App\Jobs\DataBankEmailSend;
use App\Jobs\EventNotifyJobs;
use App\Models\CustomMail;
use App\Models\DataBank;
use App\Models\DataBankCategory;
use App\Models\Domain;
use App\Models\Profile;
use Dflydev\DotAccessData\Data;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class DataBanksController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('data_bank_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = DataBank::with(['data_bank_categories'])->select(sprintf('%s.*', (new DataBank)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'data_bank_show';
                $editGate      = 'data_bank_edit';
                $deleteGate    = 'data_bank_delete';
                $crudRoutePart = 'data-banks';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->editColumn('email', function ($row) {
                return $row->email ? $row->email : '';
            });
            $table->editColumn('is_subscribe', function ($row) {
                return $row->is_subscribe ? DataBank::IS_SUBSCRIBE_SELECT[$row->is_subscribe] : '0';
            });
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->editColumn('unsubscribe_link', function ($row) {
                return $row->unsubscribe_link ? $row->unsubscribe_link : '';
            });
            $table->editColumn('data_bank_category', function ($row) {
                $labels = [];
                foreach ($row->data_bank_categories as $data_bank_category) {
                    $labels[] = sprintf('<span class="label label-info label-many">%s</span>', $data_bank_category->title_of_data_bank);
                }

                return implode(' ', $labels);
            });

            $table->rawColumns(['actions', 'placeholder', 'data_bank_category']);

            return $table->make(true);
        }

        return view('admin.dataBanks.index');
    }

    public function create()
    {
        abort_if(Gate::denies('data_bank_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data_bank_categories = DataBankCategory::pluck('title_of_data_bank', 'id');

        return view('admin.dataBanks.create', compact('data_bank_categories'));
    }

    public function store(StoreDataBankRequest $request)
    {
        //$dataBank = DataBank::create($request->all());
        $data = $request->all();
        $unsubscribe_link = Str::random(30);
        $data['unsubscribe_link'] = $unsubscribe_link;
        $dataBank = DataBank::create($data);
        $dataBank->data_bank_categories()->sync($request->input('data_bank_categories', []));
        return redirect()->route('admin.data-banks.index');
    }

    public function edit(DataBank $dataBank)
    {
        abort_if(Gate::denies('data_bank_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data_bank_categories = DataBankCategory::pluck('title_of_data_bank', 'id');

        $dataBank->load('data_bank_categories');

        return view('admin.dataBanks.edit', compact('dataBank', 'data_bank_categories'));
    }

    public function update(UpdateDataBankRequest $request, DataBank $dataBank)
    {
        $dataBank->update($request->all());
        $dataBank->data_bank_categories()->sync($request->input('data_bank_categories', []));

        return redirect()->route('admin.data-banks.index');
    }

    public function show(DataBank $dataBank)
    {
        abort_if(Gate::denies('data_bank_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $dataBank->load('data_bank_categories');

        return view('admin.dataBanks.show', compact('dataBank'));
    }

    public function destroy(DataBank $dataBank)
    {
        abort_if(Gate::denies('data_bank_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $dataBank->delete();

        return back();
    }

    public function massDestroy(MassDestroyDataBankRequest $request)
    {
        $dataBanks = DataBank::find(request('ids'));

        foreach ($dataBanks as $dataBank) {
            $dataBank->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }


public function sendEmail(){
    $emails = CustomMail::where('publication_status',1)->get();
    $dataBankCategories = DataBankCategory::where('is_active','1')->get();
    return view('admin.dataBanks.send-email', compact('emails','dataBankCategories'));
}

public function dataBankSendEmail(Request $request){
    $request->validate([
        'email_id' => 'required',
        'data_bank_categories' => 'required'
    ]);
    $message = CustomMail::findOrFail($request->input('email_id'));

    $categoryIds = $request->input('data_bank_categories');

    $dataBanks = DataBank::whereHas('data_bank_categories', function ($query) use ($categoryIds) {
        $query->whereIn('data_bank_categories.id', $categoryIds);
    })->where('is_subscribe','1')
        ->get();


    $i=0;
    foreach ($dataBanks as $dataBank){
                DataBankEmailSend::dispatch($dataBank,$message);
    }
    return response('Email sent successfully');


}

}
