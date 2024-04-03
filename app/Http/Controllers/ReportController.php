<?php

namespace App\Http\Controllers;


use App\Helpers\GeneralHelper;
use App\Models\Borrower;

use App\Models\ChartOfAccount;
use App\Models\Collateral;
use App\Models\CollateralType;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\JournalEntry;
use App\Models\Loan;
use App\Models\LoanProduct;
use App\Models\LoanRepayment;
use App\Models\LoanSchedule;
use App\Models\LoanTransaction;
use App\Models\OtherIncome;
use App\Models\Payroll;
use App\Models\ProvisionRate;
use App\Models\SavingTransaction;
use App\Models\Setting;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\View;
use PDF;
use Excel;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['sentinel', 'branch']);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function cash_flow(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        return view('report.cash_flow',
            compact('start_date',
                'end_date'));
    }



    public function loan_arrears(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        return view('report.balance_sheet',
            compact('expenses'));
    }

    public function loan_transaction(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if ($request->isMethod('post')) {

            $data = LoanRepayment::where('branch_id', session('branch_id'))->whereBetween('collection_date',
                [$start_date, $end_date])->get();

        } else {
            $data = LoanRepayment::all();
        }
        return view('report.loan_transaction',
            compact('data', 'start_date',
                'end_date'));
    }

    public function loan_classification(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;


        $data = Loan::whereIn('status', ['disbursed', 'closed', 'written_off'])->get();

        return view('report.loan_classification',
            compact('data', 'start_date',
                'end_date'));
    }





     public function loan_list(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;


        $data = Loan::whereIn('status', ['disbursed', 'closed', 'written_off'])->get();

        return view('report.loan_list',
            compact('data', 'start_date',
                'end_date'));
    }






    public function loan_product(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;


        $data = LoanProduct::all();

        return view('report.loan_product',
            compact('data', 'start_date',
                'end_date'));
    }


    public function loan_projection(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $monthly_collections = array();
        $start_date1 = date("Y-m-d");
        for ($i = 1; $i < 14; $i++) {
            $d = explode('-', $start_date1);
            //get loans in that period
            $payments = 0;
            $payments_due = 0;
            foreach (LoanSchedule::where('branch_id', session('branch_id'))->where('year', $d[0])->where('month',
                $d[1])->get() as $key) {
                if (!empty($key->loan)) {
                    if ($key->loan->status == 'disbursed' || $key->loan->status == 'written_off' || $key->loan->status == 'closed') {
                        $payments_due = $payments_due + $key->principal + $key->fees + $key->interest + $key->penalty;
                    }
                }
            }
            $payments_due = round($payments_due, 2);
            $ext = ' ' . $d[0];
            array_push($monthly_collections, array(
                'month' => date_format(date_create($start_date1),
                    'M' . $ext),
                'due' => $payments_due

            ));
            //add 1 month to start date
            $start_date1 = date_format(date_add(date_create($start_date1),
                date_interval_create_from_date_string('1 months')),
                'Y-m-d');
        }
        $monthly_collections = json_encode($monthly_collections);
        return view('report.loan_projection',
            compact('monthly_collections', 'start_date',
                'end_date'));
    }


    public function financial_report(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        return view('financial_report.data',
            compact('start_date',
                'end_date'));
    }

    public function loan_report(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        return view('loan_report.data',
            compact('start_date',
                'end_date'));
    }

    public function borrower_report(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        return view('borrower_report.data',
            compact('start_date',
                'end_date'));
    }

    public function company_report(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        return view('company_report.data',
            compact('start_date',
                'end_date'));
    }

    public function savings_report(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        return view('savings_report.data',
            compact('start_date',
                'end_date'));
    }

    public function trial_balance(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        return view('financial_report.trial_balance',
            compact('start_date',
                'end_date'));
    }

    public function trial_balance_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $pdf = PDF::loadView('financial_report.trial_balance_pdf', compact('start_date',
            'end_date'));
        return $pdf->download(trans_choice('general.trial_balance', 1) . ' : ' . $request->end_date . ".pdf");

    }

    public function trial_balance_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($start_date)) {
            $data = [];
            array_push($data, [
                trans_choice('general.trial_balance',
                    1) . " " . trans_choice('general.for', 1) . " " . trans_choice('general.period',
                    1) . ":" . $start_date . " " . trans_choice('general.to', 1) . " " . $end_date
            ]);
            array_push($data, [
                trans_choice('general.gl_code', 1),
                trans_choice('general.account', 1),
                trans_choice('general.debit', 1),
                trans_choice('general.credit', 1)
            ]);
            $credit_total = 0;
            $debit_total = 0;
            foreach (ChartOfAccount::orderBy('gl_code', 'asc')->get() as $key) {
                $cr = 0;
                $dr = 0;
                $cr = \App\Models\JournalEntry::where('account_id', $key->id)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id',
                    session('branch_id'))->sum('credit');
                $dr = \App\Models\JournalEntry::where('account_id', $key->id)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id',
                    session('branch_id'))->sum('debit');
                $credit_total = $credit_total + $cr;
                $debit_total = $debit_total + $dr;
                array_push($data, [$key->gl_code, $key->name, number_format($dr, 2), number_format($cr, 2)]);
            }
            array_push($data, [
                trans_choice('general.total', 1),
                "",
                number_format($debit_total, 2),
                number_format($credit_total, 2)
            ]);

            Excel::create(trans_choice('general.trial_balance', 1) . ' : ' . $request->end_date,
                function ($excel) use ($data) {
                    $excel->sheet('Sheet', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', false, false);
                        $sheet->mergeCells('A1:D1');
                    });

                })->download('xls');
        }
    }

    public function trial_balance_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($start_date)) {
            $data = [];
            array_push($data, [
                trans_choice('general.trial_balance',
                    1) . " " . trans_choice('general.for', 1) . " " . trans_choice('general.period',
                    1) . ":" . $start_date . " " . trans_choice('general.to', 1) . " " . $end_date
            ]);
            array_push($data, [
                trans_choice('general.gl_code', 1),
                trans_choice('general.account', 1),
                trans_choice('general.debit', 1),
                trans_choice('general.credit', 1)
            ]);
            $credit_total = 0;
            $debit_total = 0;
            foreach (ChartOfAccount::orderBy('gl_code', 'asc')->get() as $key) {
                $cr = 0;
                $dr = 0;
                $cr = \App\Models\JournalEntry::where('account_id', $key->id)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id',
                    session('branch_id'))->sum('credit');
                $dr = \App\Models\JournalEntry::where('account_id', $key->id)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id',
                    session('branch_id'))->sum('debit');
                $credit_total = $credit_total + $cr;
                $debit_total = $debit_total + $dr;
                array_push($data, [$key->gl_code, $key->name, number_format($dr, 2), number_format($cr, 2)]);
            }
            array_push($data, [
                trans_choice('general.total', 1),
                "",
                number_format($debit_total, 2),
                number_format($credit_total, 2)
            ]);

            Excel::create(trans_choice('general.trial_balance', 1) . ' : ' . $request->end_date,
                function ($excel) use ($data) {
                    $excel->sheet('Sheet', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', false, false);
                        $sheet->mergeCells('A1:D1');
                    });

                })->download('csv');
        }
    }

    public function income_statement(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        return view('financial_report.income_statement',
            compact('start_date',
                'end_date'));
    }

    public function income_statement_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $pdf = PDF::loadView('financial_report.income_statement_pdf', compact('start_date',
            'end_date'));
        return $pdf->download(trans_choice('general.income', 1) . ' ' . trans_choice('general.statement',
                1) . ' : ' . $request->end_date . ".pdf");
    }

    public function income_statement_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($start_date)) {
            $data = [];
            array_push($data, [
                trans_choice('general.income', 1) . ' ' . trans_choice('general.statement',
                    1) . ' : ' . $request->end_date
            ]);
            array_push($data, [
                trans_choice('general.gl_code', 1),
                trans_choice('general.account', 1),
                trans_choice('general.balance', 1),
            ]);
            array_push($data, [
                "",
                trans_choice('general.income', 1),
                ""
            ]);
            $total_income = 0;
            $total_expenses = 0;
            foreach (ChartOfAccount::where('account_type', 'income')->orderBy('gl_code', 'asc')->get() as $key) {
                $cr = \App\Models\JournalEntry::where('account_id', $key->id)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id',
                    session('branch_id'))->sum('credit');
                $dr = \App\Models\JournalEntry::where('account_id', $key->id)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id',
                    session('branch_id'))->sum('debit');
                $balance = $cr - $dr;
                $total_income = $total_income + $balance;
                array_push($data, [$key->gl_code, $key->name, number_format($balance, 2)]);
            }
            array_push($data, [
                "",
                trans_choice('general.total', 1) . " " . trans_choice('general.income', 1),
                number_format($total_income, 2)
            ]);
            array_push($data, [
                "",
                trans_choice('general.expense', 2),
                ""
            ]);
            foreach (ChartOfAccount::where('account_type', 'expense')->orderBy('gl_code', 'asc')->get() as $key) {
                $cr = \App\Models\JournalEntry::where('account_id', $key->id)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id',
                    session('branch_id'))->sum('credit');
                $dr = \App\Models\JournalEntry::where('account_id', $key->id)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id',
                    session('branch_id'))->sum('debit');
                $balance = $dr - $cr;
                $total_expenses = $total_expenses + $balance;
                array_push($data, [$key->gl_code, $key->name, number_format($balance, 2)]);
            }
            array_push($data, [
                "",
                trans_choice('general.total', 1) . " " . trans_choice('general.expense', 2),
                number_format($total_expenses, 2)
            ]);
            array_push($data, [
                "",
                trans_choice('general.net', 1) . " " . trans_choice('general.income', 2),
                number_format($total_income - $total_expenses, 2)
            ]);

            Excel::create(trans_choice('general.income', 1) . ' ' . trans_choice('general.statement',
                    1) . ' : ' . $request->end_date,
                function ($excel) use ($data) {
                    $excel->sheet('Sheet', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', false, false);
                        $sheet->mergeCells('A1:C1');
                    });

                })->download('xls');
        }
    }

    public function income_statement_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($start_date)) {
            $data = [];
            array_push($data, [
                trans_choice('general.income', 1) . ' ' . trans_choice('general.statement',
                    1) . ' : ' . $request->end_date
            ]);
            array_push($data, [
                trans_choice('general.gl_code', 1),
                trans_choice('general.account', 1),
                trans_choice('general.balance', 1),
            ]);
            array_push($data, [
                "",
                trans_choice('general.income', 1),
                ""
            ]);
            $total_income = 0;
            $total_expenses = 0;
            foreach (ChartOfAccount::where('account_type', 'income')->orderBy('gl_code', 'asc')->get() as $key) {
                $cr = \App\Models\JournalEntry::where('account_id', $key->id)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id',
                    session('branch_id'))->sum('credit');
                $dr = \App\Models\JournalEntry::where('account_id', $key->id)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id',
                    session('branch_id'))->sum('debit');
                $balance = $cr - $dr;
                $total_income = $total_income + $balance;
                array_push($data, [$key->gl_code, $key->name, number_format($balance, 2)]);
            }
            array_push($data, [
                "",
                trans_choice('general.total', 1) . " " . trans_choice('general.income', 1),
                number_format($total_income, 2)
            ]);
            array_push($data, [
                "",
                trans_choice('general.expense', 2),
                ""
            ]);
            foreach (ChartOfAccount::where('account_type', 'expense')->orderBy('gl_code', 'asc')->get() as $key) {
                $cr = \App\Models\JournalEntry::where('account_id', $key->id)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id',
                    session('branch_id'))->sum('credit');
                $dr = \App\Models\JournalEntry::where('account_id', $key->id)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id',
                    session('branch_id'))->sum('debit');
                $balance = $dr - $cr;
                $total_expenses = $total_expenses + $balance;
                array_push($data, [$key->gl_code, $key->name, number_format($balance, 2)]);
            }
            array_push($data, [
                "",
                trans_choice('general.total', 1) . " " . trans_choice('general.expense', 2),
                number_format($total_expenses, 2)
            ]);
            array_push($data, [
                "",
                trans_choice('general.net', 1) . " " . trans_choice('general.income', 2),
                number_format($total_income - $total_expenses, 2)
            ]);

            Excel::create(trans_choice('general.income', 1) . ' ' . trans_choice('general.statement',
                    1) . ' : ' . $request->end_date,
                function ($excel) use ($data) {
                    $excel->sheet('Sheet', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', false, false);
                        $sheet->mergeCells('A1:C1');
                    });

                })->download('csv');
        }
    }

    public function balance_sheet(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        return view('financial_report.balance_sheet',
            compact('start_date',
                'end_date'));
    }

    public function balance_sheet_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $pdf = PDF::loadView('financial_report.balance_sheet_pdf', compact('start_date',
            'end_date'));
        return $pdf->download(trans_choice('general.balance', 1) . ' ' . trans_choice('general.sheet',
                1) . ' : ' . $request->end_date . ".pdf");
    }

    public function balance_sheet_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($start_date)) {
            $data = [];
            array_push($data, [
                trans_choice('general.balance', 1) . ' ' . trans_choice('general.sheet',
                    1) . ' : ' . $request->start_date
            ]);
            array_push($data, [
                trans_choice('general.gl_code', 1),
                trans_choice('general.account', 1),
                trans_choice('general.balance', 1),
            ]);
            array_push($data, [
                trans_choice('general.asset', 2),
                "",
                ""
            ]);
            $total_liabilities = 0;
            $total_assets = 0;
            $total_equity = 0;
            $retained_earnings = 0;
            foreach (ChartOfAccount::where('account_type', 'asset')->orderBy('gl_code', 'asc')->get() as $key) {
                $cr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                    $start_date)->where('branch_id',
                    session('branch_id'))->sum('credit');
                $dr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                    $start_date)->where('branch_id',
                    session('branch_id'))->sum('debit');
                $balance = $dr - $cr;
                $total_assets = $total_assets + $balance;
                array_push($data, [$key->gl_code, $key->name, number_format($balance, 2)]);
            }
            array_push($data, [
                "",
                trans_choice('general.total', 1) . " " . trans_choice('general.asset', 2),
                number_format($total_assets, 2)
            ]);
            array_push($data, [
                "",
                trans_choice('general.liability', 2),
                ""
            ]);
            foreach (ChartOfAccount::where('account_type', 'liability')->orderBy('gl_code', 'asc')->get() as $key) {
                $cr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                    $start_date)->where('branch_id',
                    session('branch_id'))->sum('credit');
                $dr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                    $start_date)->where('branch_id',
                    session('branch_id'))->sum('debit');
                $balance = $cr - $dr;
                $total_liabilities = $total_liabilities + $balance;
                array_push($data, [$key->gl_code, $key->name, number_format($balance, 2)]);
            }
            array_push($data, [
                "",
                trans_choice('general.total', 1) . " " . trans_choice('general.liability', 2),
                number_format($total_liabilities, 2)
            ]);
            array_push($data, [
                "",
                trans_choice('general.equity', 2),
                ""
            ]);
            foreach (ChartOfAccount::where('account_type', 'equity')->orderBy('gl_code', 'asc')->get() as $key) {
                $cr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                    $start_date)->where('branch_id',
                    session('branch_id'))->sum('credit');
                $dr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                    $start_date)->where('branch_id',
                    session('branch_id'))->sum('debit');
                $balance = $cr - $dr;
                $total_equity = $total_equity + $balance;
                array_push($data, [$key->gl_code, $key->name, number_format($balance, 2)]);
            }
            array_push($data, [
                "",
                trans_choice('general.total', 1) . " " . trans_choice('general.equity', 2),
                number_format($total_equity, 2)
            ]);
            array_push($data, [
                "",
                trans_choice('general.total', 1) . " " . trans_choice('general.liability',
                    2) . " " . trans_choice('general.and', 2) . " " . trans_choice('general.equity', 2),
                number_format($total_liabilities + $total_equity, 2)
            ]);


            Excel::create(trans_choice('general.balance', 1) . ' ' . trans_choice('general.sheet',
                    1) . ' : ' . $request->start_date,
                function ($excel) use ($data) {
                    $excel->sheet('Sheet', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', false, false);
                        $sheet->mergeCells('A1:C1');
                    });

                })->download('xls');
        }
    }

    public function balance_sheet_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($start_date)) {
            $data = [];
            array_push($data, [
                trans_choice('general.balance', 1) . ' ' . trans_choice('general.sheet',
                    1) . ' : ' . $request->start_date
            ]);
            array_push($data, [
                trans_choice('general.gl_code', 1),
                trans_choice('general.account', 1),
                trans_choice('general.balance', 1),
            ]);
            array_push($data, [
                trans_choice('general.asset', 2),
                "",
                ""
            ]);
            $total_liabilities = 0;
            $total_assets = 0;
            $total_equity = 0;
            $retained_earnings = 0;
            foreach (ChartOfAccount::where('account_type', 'asset')->orderBy('gl_code', 'asc')->get() as $key) {
                $cr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                    $start_date)->where('branch_id',
                    session('branch_id'))->sum('credit');
                $dr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                    $start_date)->where('branch_id',
                    session('branch_id'))->sum('debit');
                $balance = $dr - $cr;
                $total_assets = $total_assets + $balance;
                array_push($data, [$key->gl_code, $key->name, number_format($balance, 2)]);
            }
            array_push($data, [
                "",
                trans_choice('general.total', 1) . " " . trans_choice('general.asset', 2),
                number_format($total_assets, 2)
            ]);
            array_push($data, [
                "",
                trans_choice('general.liability', 2),
                ""
            ]);
            foreach (ChartOfAccount::where('account_type', 'liability')->orderBy('gl_code', 'asc')->get() as $key) {
                $cr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                    $start_date)->where('branch_id',
                    session('branch_id'))->sum('credit');
                $dr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                    $start_date)->where('branch_id',
                    session('branch_id'))->sum('debit');
                $balance = $cr - $dr;
                $total_liabilities = $total_liabilities + $balance;
                array_push($data, [$key->gl_code, $key->name, number_format($balance, 2)]);
            }
            array_push($data, [
                "",
                trans_choice('general.total', 1) . " " . trans_choice('general.liability', 2),
                number_format($total_liabilities, 2)
            ]);
            array_push($data, [
                "",
                trans_choice('general.equity', 2),
                ""
            ]);
            foreach (ChartOfAccount::where('account_type', 'equity')->orderBy('gl_code', 'asc')->get() as $key) {
                $cr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                    $start_date)->where('branch_id',
                    session('branch_id'))->sum('credit');
                $dr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                    $start_date)->where('branch_id',
                    session('branch_id'))->sum('debit');
                $balance = $cr - $dr;
                $total_equity = $total_equity + $balance;
                array_push($data, [$key->gl_code, $key->name, number_format($balance, 2)]);
            }
            array_push($data, [
                "",
                trans_choice('general.total', 1) . " " . trans_choice('general.equity', 2),
                number_format($total_equity, 2)
            ]);
            array_push($data, [
                "",
                trans_choice('general.total', 1) . " " . trans_choice('general.liability',
                    2) . " " . trans_choice('general.and', 2) . " " . trans_choice('general.equity', 2),
                number_format($total_liabilities + $total_equity, 2)
            ]);


            Excel::create(trans_choice('general.balance', 1) . ' ' . trans_choice('general.sheet',
                    1) . ' : ' . $request->start_date,
                function ($excel) use ($data) {
                    $excel->sheet('Sheet', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', false, false);
                        $sheet->mergeCells('A1:C1');
                    });

                })->download('csv');
        }
    }

    public function expected_repayments(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $expect_report = [];
        $users_rang = [];
        $routes_rang = [];

        foreach (\App\Models\LoanTransaction::where('transaction_type', 'repayment')->where('reversed', 0)->whereBetween('date',
        [$start_date, $end_date])->orderBy('user_id')->get() as $key) {
            if (!in_array($key->user_id, $users_rang)) {
                array_push($users_rang, $key->user_id);
            }

            $loan = \App\Models\Loan::where('id', $key->loan_id)->first();
            $route = \App\Models\LoanProduct::where('id', $loan->loan_product_id)->first();
            if (!in_array($route->id, $routes_rang)) {
                array_push($routes_rang, $route->id);
            }
        }

        foreach ($users_rang as $uuu => $user_id) {
            $user = \App\Models\User::where('id', $user_id)->first();

            foreach ($routes_rang as $rrr => $router) {

                $loan_id_rang = [];
                foreach (\App\Models\Loan::where('loan_product_id', $router)->get() as $lloan) {
                    array_push($loan_id_rang, $lloan->id);
                }
                $route = \App\Models\LoanProduct::where('id', $router)->first();

                $principal_total_data = 0;
                $interest_total_data = 0;
                $fees_total_data = 0;
                $penalty_total_data = 0;
                $total_total_data = 0;
                $pay_count = 0;

                foreach (\App\Models\LoanTransaction::where('transaction_type', 'repayment')->where('reversed', 0)->whereBetween('date', [$start_date, $end_date])->where('user_id', $user_id)->whereIn('loan_id', $loan_id_rang)->get() as $key) {

                    $principal = \App\Models\JournalEntry::where('loan_transaction_id', $key->id)->where('reversed',0)->where('name', "Principal Repayment")->sum('credit');

                    $interest = \App\Models\JournalEntry::where('loan_transaction_id', $key->id)->where('reversed',0)->where('name', "Interest Repayment")->sum('credit');

                    $fees = \App\Models\JournalEntry::where('loan_transaction_id', $key->id)->where('reversed',0)->where('name', "Fees Repayment")->sum('credit');

                    $penalty = \App\Models\JournalEntry::where('loan_transaction_id', $key->id)->where('reversed',0)->where('name', "Penalty Repayment")->sum('credit');

                    $total = \App\Models\LoanTransaction::where('id', $key->id)->where('reversed', 0)->where('transaction_type', "repayment")->sum('credit');

                    $principal_total_data = $principal_total_data + $principal;
                    $interest_total_data = $interest_total_data + $interest;
                    $fees_total_data = $fees_total_data + $fees;
                    $penalty_total_data = $penalty_total_data + $penalty;
                    $total_total_data = $total_total_data + $total;

                    $pay_count = $pay_count + 1;
                }

                if ($total_total_data > 0) {
                    array_push($expect_report, array(
                        'user_id' => $user_id,
                        'user' => $user->first_name.' '.$user->last_name,
                        'route_id' => $router,
                        'route' => $route->name,
                        'principal' => $principal_total_data,
                        'interest' => $interest_total_data,
                        'fees' => $fees_total_data,
                        'penalty' => $penalty_total_data,
                        'total' => $total_total_data,
                        'count' => $pay_count
                    ));
                }
            }
        }

        return view('loan_report.expected_repayments',
            compact('start_date',
                'end_date', 'expect_report', 'users_rang', 'routes_rang'));
    }

    public function expected_repayments_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $due_items = GeneralHelper::loans_due_items($start_date, $end_date);
        $paid_items = GeneralHelper::loans_paid_items($start_date, $end_date);
        $pdf = PDF::loadView('loan_report.expected_repayments_pdf', compact('start_date',
            'end_date', 'due_items', 'paid_items'));
        return $pdf->download(trans_choice('general.expected', 1) . ' ' . trans_choice('general.repayment',
                2) . ".pdf");
    }

    public function expected_repayments_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $due_items = GeneralHelper::loans_due_items($start_date, $end_date);
        $paid_items = GeneralHelper::loans_paid_items($start_date, $end_date);

        $data = [];
        array_push($data, [
            trans_choice('general.expected', 1) . ' ' . trans_choice('general.repayment',
                2)
        ]);
        array_push($data, [
            "",
            trans_choice('general.principal', 1),
            trans_choice('general.interest', 1),
            trans_choice('general.fee', 2),
            trans_choice('general.penalty', 2),
            trans_choice('general.total', 1),
        ]);
        array_push($data, [
            trans_choice('general.expected', 1),
            number_format($due_items["principal"], 2),
            number_format($due_items["interest"], 2),
            number_format($due_items["fees"], 2),
            number_format($due_items["penalty"], 2),
            number_format($due_items["principal"] + $due_items["interest"] + $due_items["fees"] + $due_items["penalty"],
                2),
        ]);
        array_push($data, [
            trans_choice('general.actual', 1),
            number_format($paid_items["principal"], 2),
            number_format($paid_items["interest"], 2),
            number_format($paid_items["fees"], 2),
            number_format($paid_items["penalty"], 2),
            number_format($paid_items["principal"] + $paid_items["interest"] + $paid_items["fees"] + $paid_items["penalty"],
                2),
        ]);
        array_push($data, [
            trans_choice('general.balance', 1),
            number_format($due_items["principal"] - $paid_items["principal"], 2),
            number_format($due_items["interest"] - $paid_items["interest"], 2),
            number_format($due_items["fees"] - $paid_items["fees"], 2),
            number_format($due_items["penalty"] - $paid_items["penalty"], 2),
            number_format(($due_items["principal"] + $due_items["interest"] + $due_items["fees"] + $due_items["penalty"]) - ($paid_items["principal"] + $paid_items["interest"] + $paid_items["fees"] + $paid_items["penalty"]),
                2),
        ]);

        Excel::create(trans_choice('general.expected', 1) . ' ' . trans_choice('general.repayment',
                2),
            function ($excel) use ($data) {
                $excel->sheet('Sheet', function ($sheet) use ($data) {
                    $sheet->fromArray($data, null, 'A1', false, false);
                    $sheet->mergeCells('A1:F1');
                });

            })->download('xls');
    }

    public function expected_repayments_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $due_items = GeneralHelper::loans_due_items($start_date, $end_date);
        $paid_items = GeneralHelper::loans_paid_items($start_date, $end_date);

        $data = [];
        array_push($data, [
            trans_choice('general.expected', 1) . ' ' . trans_choice('general.repayment',
                2)
        ]);
        array_push($data, [
            "",
            trans_choice('general.principal', 1),
            trans_choice('general.interest', 1),
            trans_choice('general.fee', 2),
            trans_choice('general.penalty', 2),
            trans_choice('general.total', 1),
        ]);
        array_push($data, [
            trans_choice('general.expected', 1),
            number_format($due_items["principal"], 2),
            number_format($due_items["interest"], 2),
            number_format($due_items["fees"], 2),
            number_format($due_items["penalty"], 2),
            number_format($due_items["principal"] + $due_items["interest"] + $due_items["fees"] + $due_items["penalty"],
                2),
        ]);
        array_push($data, [
            trans_choice('general.actual', 1),
            number_format($paid_items["principal"], 2),
            number_format($paid_items["interest"], 2),
            number_format($paid_items["fees"], 2),
            number_format($paid_items["penalty"], 2),
            number_format($paid_items["principal"] + $paid_items["interest"] + $paid_items["fees"] + $paid_items["penalty"],
                2),
        ]);
        array_push($data, [
            trans_choice('general.balance', 1),
            number_format($due_items["principal"] - $paid_items["principal"], 2),
            number_format($due_items["interest"] - $paid_items["interest"], 2),
            number_format($due_items["fees"] - $paid_items["fees"], 2),
            number_format($due_items["penalty"] - $paid_items["penalty"], 2),
            number_format(($due_items["principal"] + $due_items["interest"] + $due_items["fees"] + $due_items["penalty"]) - ($paid_items["principal"] + $paid_items["interest"] + $paid_items["fees"] + $paid_items["penalty"]),
                2),
        ]);

        Excel::create(trans_choice('general.expected', 1) . ' ' . trans_choice('general.repayment',
                2),
            function ($excel) use ($data) {
                $excel->sheet('Sheet', function ($sheet) use ($data) {
                    $sheet->fromArray($data, null, 'A1', false, false);
                    $sheet->mergeCells('A1:F1');
                });

            })->download('csv');
    }

    public function repayments_report(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($start_date)) {
            $data = LoanTransaction::where('transaction_type',
                'repayment')->where('reversed', 0)->whereBetween('date',
                [$start_date, $end_date])->get();
        }
        else {
            $data = [];
        }
        return view('loan_report.repayments_report',
            compact('start_date',
                'end_date', 'data'));
    }

    public function repayments_report_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($start_date)) {
            $data = LoanTransaction::where('transaction_type',
                'repayment')->where('reversed', 0)->where('branch_id',
                session('branch_id'))->whereBetween('date',
                [$start_date, $end_date])->get();
            $pdf = PDF::loadView('loan_report.repayments_report_pdf', compact('start_date',
                'end_date', 'data'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download(trans_choice('general.repayment', 2) . ' ' . trans_choice('general.report',
                    1) . ".pdf");
        }


    }

    public function repayments_report_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($start_date)) {
            $data = [];
            array_push($data, [
                trans_choice('general.repayment', 1) . ' ' . trans_choice('general.report',
                    1)
            ]);
            array_push($data, [
                trans_choice('general.id', 1),
                trans_choice('general.borrower', 1),
                trans_choice('general.principal', 1),
                trans_choice('general.interest', 1),
                trans_choice('general.fee', 2),
                trans_choice('general.penalty', 1),
                trans_choice('general.total', 1),
                trans_choice('general.date', 1),
                trans_choice('general.receipt', 1),
                trans_choice('general.payment', 1) . " " . trans_choice('general.method', 1),
            ]);
            $total_principal = 0;
            $total_fees = 0;
            $total_interest = 0;
            $total_penalty = 0;
            foreach (LoanTransaction::where('transaction_type',
                'repayment')->where('reversed', 0)->where('branch_id',
                session('branch_id'))->whereBetween('date',
                [$start_date, $end_date])->get() as $key) {
                $principal = JournalEntry::where('loan_transaction_id', $key->id)->where('reversed',
                    0)->where('name', "Principal Repayment")->sum('credit');
                $interest = JournalEntry::where('loan_transaction_id', $key->id)->where('reversed',
                    0)->where('name', "Interest Repayment")->sum('credit');
                $fees = JournalEntry::where('loan_transaction_id', $key->id)->where('reversed',
                    0)->where('name', "Fees Repayment")->sum('credit');
                $penalty = JournalEntry::where('loan_transaction_id', $key->id)->where('reversed',
                    0)->where('name', "Penalty Repayment")->sum('credit');
                $total_principal = $total_principal + $principal;
                $total_interest = $total_interest + $interest;
                $total_fees = $total_fees + $fees;
                $total_penalty = $total_penalty + $penalty;
                if (!empty($key->borrower)) {
                    $borrower = $key->borrower->first_name . " " . $key->borrower->last_name;
                } else {
                    $borrower = "";
                }
                if (!empty($key->loan_repayment_method)) {
                    $loan_repayment_method = $key->loan_repayment_method->name;
                } else {
                    $loan_repayment_method = "";
                }
                array_push($data, [
                    $key->id,
                    $borrower,
                    number_format($principal, 2),
                    number_format($interest, 2),
                    number_format($fees, 2),
                    number_format($penalty, 2),
                    number_format($principal + $interest + $fees + $penalty, 2),
                    $key->date,
                    $key->receipt,
                    $loan_repayment_method,
                ]);
            }
            array_push($data, [
                "",
                "",
                number_format($total_principal, 2),
                number_format($total_interest, 2),
                number_format($total_fees, 2),
                number_format($total_penalty, 2),
                number_format($total_principal + $total_interest + $total_fees + $total_penalty, 2),
                "",
                "",
                "",
            ]);

            Excel::create(trans_choice('general.report', 2) . ' ' . trans_choice('general.repayment',
                    2),
                function ($excel) use ($data) {
                    $excel->sheet('Sheet', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', false, false);
                        $sheet->mergeCells('A1:J1');
                    });

                })->download('xls');
        }


    }

    public function repayments_report_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($start_date)) {
            $data = [];
            array_push($data, [
                trans_choice('general.repayment', 1) . ' ' . trans_choice('general.report',
                    1)
            ]);
            array_push($data, [
                trans_choice('general.id', 1),
                trans_choice('general.borrower', 1),
                trans_choice('general.principal', 1),
                trans_choice('general.interest', 1),
                trans_choice('general.fee', 2),
                trans_choice('general.penalty', 1),
                trans_choice('general.total', 1),
                trans_choice('general.date', 1),
                trans_choice('general.receipt', 1),
                trans_choice('general.payment', 1) . " " . trans_choice('general.method', 1),
            ]);
            $total_principal = 0;
            $total_fees = 0;
            $total_interest = 0;
            $total_penalty = 0;
            foreach (LoanTransaction::where('transaction_type',
                'repayment')->where('reversed', 0)->where('branch_id',
                session('branch_id'))->whereBetween('date',
                [$start_date, $end_date])->get() as $key) {
                $principal = JournalEntry::where('loan_transaction_id', $key->id)->where('reversed',
                    0)->where('transaction_sub_type', 'repayment_principal')->sum('credit');
                $interest = JournalEntry::where('loan_transaction_id', $key->id)->where('reversed',
                    0)->where('transaction_sub_type', 'repayment_interest')->sum('credit');
                $fees = JournalEntry::where('loan_transaction_id', $key->id)->where('reversed',
                    0)->where('transaction_sub_type', 'repayment_fees')->sum('credit');
                $penalty = JournalEntry::where('loan_transaction_id', $key->id)->where('reversed',
                    0)->where('transaction_sub_type', 'repayment_penalty')->sum('credit');
                $total_principal = $total_principal + $principal;
                $total_interest = $total_interest + $interest;
                $total_fees = $total_fees + $fees;
                $total_penalty = $total_penalty + $penalty;
                if (!empty($key->borrower)) {
                    $borrower = $key->borrower->first_name . " " . $key->borrower->last_name;
                } else {
                    $borrower = "";
                }
                if (!empty($key->loan_repayment_method)) {
                    $loan_repayment_method = $key->loan_repayment_method->name;
                } else {
                    $loan_repayment_method = "";
                }
                array_push($data, [
                    $key->id,
                    $borrower,
                    number_format($principal, 2),
                    number_format($interest, 2),
                    number_format($fees, 2),
                    number_format($penalty, 2),
                    number_format($principal + $interest + $fees + $penalty, 2),
                    $key->date,
                    $key->receipt,
                    $loan_repayment_method,
                ]);
            }
            array_push($data, [
                "",
                "",
                number_format($total_principal, 2),
                number_format($total_interest, 2),
                number_format($total_fees, 2),
                number_format($total_penalty, 2),
                number_format($total_principal + $total_interest + $total_fees + $total_penalty, 2),
                "",
                "",
                "",
            ]);

            Excel::create(trans_choice('general.repayment', 2) . ' ' . trans_choice('general.report',
                    2),
                function ($excel) use ($data) {
                    $excel->sheet('Sheet', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', false, false);
                        $sheet->mergeCells('A1:J1');
                    });

                })->download('csv');
        }


    }

    public function collection_sheet(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $user_id = $request->user_id;
        $users = [];
        $users["-1"] = trans_choice('general.all', 1);
        foreach (User::all() as $key) {
            $users[$key->id] = $key->first_name . ' ' . $key->last_name;
        }
        if (!empty($start_date)) {
            //get disbursed loans within specified period and officer
            if ($request->user_id == "-1") {
                $data = Loan::where('status', 'disbursed')->where('branch_id',
                    session('branch_id'))->get();
            } else {
                $data = Loan::where('loan_officer_id', $request->user_id)->where('branch_id',
                    session('branch_id'))->where('status', 'disbursed')->get();
            }
        }
        return view('loan_report.collection_sheet',
            compact('start_date',
                'end_date', 'data', 'users', 'user_id'));
    }

    public function collection_sheet_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $user_id = $request->user_id;
        if (!empty($start_date)) {
            //get disbursed loans within specified period and officer

            if ($request->user_id == "-1") {
                $data = Loan::where('status', 'disbursed')->where('branch_id',
                    session('branch_id'))->get();
            } else {
                $data = Loan::where('loan_officer_id', $request->user_id)->where('branch_id',
                    session('branch_id'))->where('status', 'disbursed')->get();
            }
            $pdf = PDF::loadView('loan_report.collection_sheet_pdf', compact('start_date',
                'end_date', 'user_id', 'data'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download(trans_choice('general.collection', 1) . ' ' . trans_choice('general.sheet',
                    2) . ".pdf");
        }


    }

    public function collection_sheet_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($start_date)) {
            if ($request->user_id == "-1") {
                $ldata = Loan::where('status', 'disbursed')->where('branch_id',
                    session('branch_id'))->get();
            } else {
                $ldata = Loan::where('loan_officer_id', $request->user_id)->where('branch_id',
                    session('branch_id'))->where('status', 'disbursed')->get();
            }
            $data = [];
            array_push($data, [
                trans_choice('general.collection', 1) . ' ' . trans_choice('general.sheet',
                    2)
            ]);
            array_push($data, [
                trans_choice('general.loan_officer', 1),
                trans_choice('general.borrower', 1),
                trans_choice('general.phone', 1),
                trans_choice('general.loan', 1) . " " . trans_choice('general.id', 1),
                trans_choice('general.product', 1),
                trans_choice('general.expected', 1) . " " . trans_choice('general.repayment',
                    1) . " " . trans_choice('general.date', 1),
                trans_choice('general.maturity', 1) . " " . trans_choice('general.date',
                    1),
                trans_choice('general.expected', 1) . " " . trans_choice('general.amount',
                    1),
                trans_choice('general.due', 1),
                trans_choice('general.outstanding', 1),
            ]);
            $total_outstanding = 0;
            $total_due = 0;
            $total_expected = 0;
            $total_actual = 0;
            foreach ($ldata as $key) {
                $schedule = \App\Models\LoanSchedule::where('loan_id', $key->id)->whereBetween('due_date',
                    [$start_date, $end_date])->orderBy('due_date', 'desc')->limit(1)->first();
                if (!empty($schedule)) {
                    $balance = \App\Helpers\GeneralHelper::loan_total_balance($key->id);
                    $loan_due_items = \App\Helpers\GeneralHelper::loan_due_items($key->id,
                        $key->release_date, $schedule->due_date);
                    $loan_paid_items = \App\Helpers\GeneralHelper::loan_paid_items($key->id,
                        $key->release_date, $schedule->due_date);
                    $expected = $schedule->principal + $schedule->interest + $schedule->fees + $schedule->panalty;
                    $due = ($loan_due_items["principal"] + $loan_due_items["interest"] + $loan_due_items["fees"] + $loan_due_items["penalty"]) - ($loan_paid_items["principal"] + $loan_paid_items["interest"] + $loan_paid_items["fees"] + $loan_paid_items["penalty"]);
                    if ($due < 0) {
                        $actual = $expected;
                    } else {
                        $actual = 0;
                    }
                    $total_outstanding = $total_outstanding + $balance;
                    $total_due = $total_due + $due;
                    $total_expected = $total_expected + $expected;
                    $total_actual = $total_actual + $actual;
                }
                if (!empty($key->borrower)) {
                    $borrower = $key->borrower->first_name . " " . $key->borrower->last_name;
                    $borrower_phone = $key->borrower->mobile;
                } else {
                    $borrower = "";
                    $borrower_phone = "";
                }
                if (!empty($key->loan_officer)) {
                    $loan_officer = $key->loan_officer->first_name . " " . $key->loan_officer->last_name;
                } else {
                    $loan_officer = "";
                }
                if (!empty($key->loan_product)) {
                    $loan_product = $key->loan_product->name;
                } else {
                    $loan_product = "";
                }
                array_push($data, [
                    $loan_officer,
                    $borrower,
                    $borrower_phone,
                    $key->id,
                    $loan_product,
                    $schedule->due_date,
                    $key->maturity_date,
                    number_format($expected, 2),
                    number_format($due, 2),
                    number_format($balance, 2)
                ]);
            }
            array_push($data, [
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                number_format($total_expected, 2),
                number_format($total_due, 2),
                number_format($total_outstanding, 2)
            ]);

            Excel::create(trans_choice('general.collection', 1) . ' ' . trans_choice('general.sheet',
                    2),
                function ($excel) use ($data) {
                    $excel->sheet('Sheet', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', false, false);
                        $sheet->mergeCells('A1:J1');
                    });

                })->download('xls');
        }


    }

    public function collection_sheet_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($start_date)) {
            if ($request->user_id == "-1") {
                $ldata = Loan::where('status', 'disbursed')->where('branch_id',
                    session('branch_id'))->get();
            } else {
                $ldata = Loan::where('loan_officer_id', $request->user_id)->where('branch_id',
                    session('branch_id'))->where('status', 'disbursed')->get();
            }
            $data = [];
            array_push($data, [
                trans_choice('general.collection', 1) . ' ' . trans_choice('general.sheet',
                    2)
            ]);
            array_push($data, [
                trans_choice('general.loan_officer', 1),
                trans_choice('general.borrower', 1),
                trans_choice('general.phone', 1),
                trans_choice('general.loan', 1) . " " . trans_choice('general.id', 1),
                trans_choice('general.product', 1),
                trans_choice('general.expected', 1) . " " . trans_choice('general.repayment',
                    1) . " " . trans_choice('general.date', 1),
                trans_choice('general.maturity', 1) . " " . trans_choice('general.date',
                    1),
                trans_choice('general.expected', 1) . " " . trans_choice('general.amount',
                    1),
                trans_choice('general.due', 1),
                trans_choice('general.outstanding', 1),
            ]);
            $total_outstanding = 0;
            $total_due = 0;
            $total_expected = 0;
            $total_actual = 0;
            foreach ($ldata as $key) {
                $schedule = \App\Models\LoanSchedule::where('loan_id', $key->id)->whereBetween('due_date',
                    [$start_date, $end_date])->orderBy('due_date', 'desc')->limit(1)->first();
                if (!empty($schedule)) {
                    $balance = \App\Helpers\GeneralHelper::loan_total_balance($key->id);
                    $loan_due_items = \App\Helpers\GeneralHelper::loan_due_items($key->id,
                        $key->release_date, $schedule->due_date);
                    $loan_paid_items = \App\Helpers\GeneralHelper::loan_paid_items($key->id,
                        $key->release_date, $schedule->due_date);
                    $expected = $schedule->principal + $schedule->interest + $schedule->fees + $schedule->panalty;
                    $due = ($loan_due_items["principal"] + $loan_due_items["interest"] + $loan_due_items["fees"] + $loan_due_items["penalty"]) - ($loan_paid_items["principal"] + $loan_paid_items["interest"] + $loan_paid_items["fees"] + $loan_paid_items["penalty"]);
                    if ($due < 0) {
                        $actual = $expected;
                    } else {
                        $actual = 0;
                    }
                    $total_outstanding = $total_outstanding + $balance;
                    $total_due = $total_due + $due;
                    $total_expected = $total_expected + $expected;
                    $total_actual = $total_actual + $actual;
                }
                if (!empty($key->borrower)) {
                    $borrower = $key->borrower->first_name . " " . $key->borrower->last_name;
                    $borrower_phone = $key->borrower->mobile;
                } else {
                    $borrower = "";
                    $borrower_phone = "";
                }
                if (!empty($key->loan_officer)) {
                    $loan_officer = $key->loan_officer->first_name . " " . $key->loan_officer->last_name;
                } else {
                    $loan_officer = "";
                }
                if (!empty($key->loan_product)) {
                    $loan_product = $key->loan_product->name;
                } else {
                    $loan_product = "";
                }
                array_push($data, [
                    $loan_officer,
                    $borrower,
                    $borrower_phone,
                    $key->id,
                    $loan_product,
                    $schedule->due_date,
                    $key->maturity_date,
                    number_format($expected, 2),
                    number_format($due, 2),
                    number_format($balance, 2)
                ]);
            }
            array_push($data, [
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                number_format($total_expected, 2),
                number_format($total_due, 2),
                number_format($total_outstanding, 2)
            ]);

            Excel::create(trans_choice('general.collection', 1) . ' ' . trans_choice('general.sheet',
                    2),
                function ($excel) use ($data) {
                    $excel->sheet('Sheet', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', false, false);
                        $sheet->mergeCells('A1:J1');
                    });

                })->download('csv');
        }


    }

    public function arrears_report(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        return view('loan_report.arrears_report',
            compact('start_date',
                'end_date'));
    }

    public function arrears_report_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        if (!empty($end_date)) {
            $pdf = PDF::loadView('loan_report.arrears_report_pdf', compact('start_date',
                'end_date'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download(trans_choice('general.arrears', 1) . ' ' . trans_choice('general.report',
                    2) . ".pdf");
        }


    }

    public function arrears_report_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($end_date)) {

            $data = [];
            array_push($data, [
                trans_choice('general.collection', 1) . ' ' . trans_choice('general.sheet',
                    2)
            ]);
            array_push($data, [
                trans_choice('general.loan_officer', 1),
                trans_choice('general.borrower', 1),
                trans_choice('general.phone', 1),
                trans_choice('general.loan', 1) . " " . trans_choice('general.id', 1),
                trans_choice('general.product', 1),
                trans_choice('general.amount', 1),
                trans_choice('general.disbursed', 1),
                trans_choice('general.maturity', 1) . " " . trans_choice('general.date',
                    1),
                trans_choice('general.principal', 1),
                trans_choice('general.interest', 1),
                trans_choice('general.fee', 2),
                trans_choice('general.penalty', 1),
                trans_choice('general.outstanding', 1),
                trans_choice('general.due', 1),
                trans_choice('general.day', 2) . " " . trans_choice('general.in',
                    1) . " " . trans_choice('general.arrears', 2),
                trans_choice('general.day', 2) . " " . trans_choice('general.since',
                    1) . " " . trans_choice('general.payment',
                    1)
            ]);
            $total_outstanding = 0;
            $total_due = 0;
            $total_principal = 0;
            $total_interest = 0;
            $total_fees = 0;
            $total_penalty = 0;
            $total_amount = 0;
            foreach (Loan::where('first_payment_date', '<=', $end_date)->where('branch_id',
                session('branch_id'))->where('status', 'disbursed')->orderBy('release_date', 'asc')->get() as $key) {
                $loan_due_items = GeneralHelper::loan_due_items($key->id,
                    $key->release_date, $end_date);
                $loan_paid_items = GeneralHelper::loan_paid_items($key->id,
                    $key->release_date, $end_date);
                $balance = GeneralHelper::loan_total_balance($key->id);
                $due = ($loan_due_items["principal"] + $loan_due_items["interest"] + $loan_due_items["fees"] + $loan_due_items["penalty"]) - ($loan_paid_items["principal"] + $loan_paid_items["interest"] + $loan_paid_items["fees"] + $loan_paid_items["penalty"]);
                $principal = $loan_due_items["principal"];
                $interest = $loan_due_items["interest"];
                $fees = $loan_due_items["fees"];
                $penalty = $loan_due_items["penalty"];
                if ($due > 0) {
                    $total_outstanding = $total_outstanding + $balance;
                    $total_due = $total_due + $due;
                    $total_principal = $total_principal + $principal;
                    $total_interest = $total_interest + $interest;
                    $total_fees = $total_fees + $fees;
                    $total_penalty = $total_penalty + $penalty;
                    $total_amount = $total_amount + $key->principal;
                    //lets find arrears information
                    $schedules = LoanSchedule::where('loan_id', $key->id)->where('due_date', '<=',
                        $end_date)->orderBy('due_date', 'asc')->get();
                    $payments = $loan_paid_items["principal"] + $loan_paid_items["interest"] + $loan_paid_items["fees"] + $loan_paid_items["penalty"];
                    if ($payments > 0) {
                        foreach ($schedules as $schedule) {
                            if ($payments > $schedule->principal + $schedule->interest + $schedule->penalty + $schedule->fees) {
                                $payments = $payments - ($schedule->principal + $schedule->interest + $schedule->penalty + $schedule->fees);
                            } else {
                                $payments = 0;
                                $overdue_date = $schedule->due_date;
                                break;
                            }
                        }
                    } else {
                        $overdue_date = $schedules->first()->due_date;
                    }
                    $date1 = new \DateTime($overdue_date);
                    $date2 = new \DateTime($end_date);
                    $days_arrears = $date2->diff($date1)->format("%a");
                    $transaction = LoanTransaction::where('loan_id',
                        $key->id)->where('transaction_type',
                        'repayment')->where('reversed', 0)->orderBy('date', 'desc')->first();
                    if (!empty($transaction)) {
                        $date2 = new \DateTime($transaction->date);
                        $date1 = new \DateTime($end_date);
                        $days_last_payment = $date2->diff($date1)->format("%r%a");
                    } else {
                        $days_last_payment = 0;
                    }
                }
                if (!empty($key->borrower)) {
                    $borrower = $key->borrower->first_name . " " . $key->borrower->last_name;
                    $borrower_phone = $key->borrower->mobile;
                } else {
                    $borrower = "";
                    $borrower_phone = "";
                }
                if (!empty($key->loan_officer)) {
                    $loan_officer = $key->loan_officer->first_name . " " . $key->loan_officer->last_name;
                } else {
                    $loan_officer = "";
                }
                if (!empty($key->loan_product)) {
                    $loan_product = $key->loan_product->name;
                } else {
                    $loan_product = "";
                }
                if ($due > 0) {
                    array_push($data, [
                        $loan_officer,
                        $borrower,
                        $borrower_phone,
                        $key->id,
                        $loan_product,
                        number_format($key->principal, 2),
                        $key->release_date,
                        $key->maturity_date,
                        number_format($principal, 2),
                        number_format($interest, 2),
                        number_format($fees, 2),
                        number_format($penalty, 2),
                        number_format($due, 2),
                        number_format($balance, 2),
                        number_format($days_arrears, 2),
                        number_format($days_last_payment, 2),
                    ]);
                }

            }
            array_push($data, [
                "",
                "",
                "",
                "",
                "",
                number_format($total_amount, 2),
                "",
                "",
                number_format($total_principal, 2),
                number_format($total_interest, 2),
                number_format($total_fees, 2),
                number_format($total_penalty, 2),
                number_format($total_outstanding, 2),
                number_format($total_due, 2),
                "",
                "",
            ]);

            Excel::create(trans_choice('general.arrears', 1) . ' ' . trans_choice('general.report',
                    2),
                function ($excel) use ($data) {
                    $excel->sheet('Sheet', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', false, false);
                        $sheet->mergeCells('A1:P1');
                    });

                })->download('xls');
        }


    }

    public function arrears_report_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($end_date)) {

            $data = [];
            array_push($data, [
                trans_choice('general.collection', 1) . ' ' . trans_choice('general.sheet',
                    2)
            ]);
            array_push($data, [
                trans_choice('general.loan_officer', 1),
                trans_choice('general.borrower', 1),
                trans_choice('general.phone', 1),
                trans_choice('general.loan', 1) . " " . trans_choice('general.id', 1),
                trans_choice('general.product', 1),
                trans_choice('general.amount', 1),
                trans_choice('general.disbursed', 1),
                trans_choice('general.maturity', 1) . " " . trans_choice('general.date',
                    1),
                trans_choice('general.principal', 1),
                trans_choice('general.interest', 1),
                trans_choice('general.fee', 2),
                trans_choice('general.penalty', 1),
                trans_choice('general.outstanding', 1),
                trans_choice('general.due', 1),
                trans_choice('general.day', 2) . " " . trans_choice('general.in',
                    1) . " " . trans_choice('general.arrears', 2),
                trans_choice('general.day', 2) . " " . trans_choice('general.since',
                    1) . " " . trans_choice('general.payment',
                    1)
            ]);
            $total_outstanding = 0;
            $total_due = 0;
            $total_principal = 0;
            $total_interest = 0;
            $total_fees = 0;
            $total_penalty = 0;
            $total_amount = 0;
            foreach (Loan::where('first_payment_date', '<=', $end_date)->where('branch_id',
                session('branch_id'))->where('status', 'disbursed')->orderBy('release_date', 'asc')->get() as $key) {
                $loan_due_items = GeneralHelper::loan_due_items($key->id,
                    $key->release_date, $end_date);
                $loan_paid_items = GeneralHelper::loan_paid_items($key->id,
                    $key->release_date, $end_date);
                $balance = GeneralHelper::loan_total_balance($key->id);
                $due = ($loan_due_items["principal"] + $loan_due_items["interest"] + $loan_due_items["fees"] + $loan_due_items["penalty"]) - ($loan_paid_items["principal"] + $loan_paid_items["interest"] + $loan_paid_items["fees"] + $loan_paid_items["penalty"]);
                $principal = $loan_due_items["principal"];
                $interest = $loan_due_items["interest"];
                $fees = $loan_due_items["fees"];
                $penalty = $loan_due_items["penalty"];
                if ($due > 0) {
                    $total_outstanding = $total_outstanding + $balance;
                    $total_due = $total_due + $due;
                    $total_principal = $total_principal + $principal;
                    $total_interest = $total_interest + $interest;
                    $total_fees = $total_fees + $fees;
                    $total_penalty = $total_penalty + $penalty;
                    $total_amount = $total_amount + $key->principal;
                    //lets find arrears information
                    $schedules = LoanSchedule::where('loan_id', $key->id)->where('due_date', '<=',
                        $end_date)->orderBy('due_date', 'asc')->get();
                    $payments = $loan_paid_items["principal"] + $loan_paid_items["interest"] + $loan_paid_items["fees"] + $loan_paid_items["penalty"];
                    if ($payments > 0) {
                        foreach ($schedules as $schedule) {
                            if ($payments > $schedule->principal + $schedule->interest + $schedule->penalty + $schedule->fees) {
                                $payments = $payments - ($schedule->principal + $schedule->interest + $schedule->penalty + $schedule->fees);
                            } else {
                                $payments = 0;
                                $overdue_date = $schedule->due_date;
                                break;
                            }
                        }
                    } else {
                        $overdue_date = $schedules->first()->due_date;
                    }
                    $date1 = new \DateTime($overdue_date);
                    $date2 = new \DateTime($end_date);
                    $days_arrears = $date2->diff($date1)->format("%a");
                    $transaction = LoanTransaction::where('loan_id',
                        $key->id)->where('transaction_type',
                        'repayment')->where('reversed', 0)->orderBy('date', 'desc')->first();
                    if (!empty($transaction)) {
                        $date2 = new \DateTime($transaction->date);
                        $date1 = new \DateTime($end_date);
                        $days_last_payment = $date2->diff($date1)->format("%r%a");
                    } else {
                        $days_last_payment = 0;
                    }
                }
                if (!empty($key->borrower)) {
                    $borrower = $key->borrower->first_name . " " . $key->borrower->last_name;
                    $borrower_phone = $key->borrower->mobile;
                } else {
                    $borrower = "";
                    $borrower_phone = "";
                }
                if (!empty($key->loan_officer)) {
                    $loan_officer = $key->loan_officer->first_name . " " . $key->loan_officer->last_name;
                } else {
                    $loan_officer = "";
                }
                if (!empty($key->loan_product)) {
                    $loan_product = $key->loan_product->name;
                } else {
                    $loan_product = "";
                }
                if ($due > 0) {
                    array_push($data, [
                        $loan_officer,
                        $borrower,
                        $borrower_phone,
                        $key->id,
                        $loan_product,
                        number_format($key->principal, 2),
                        $key->release_date,
                        $key->maturity_date,
                        number_format($principal, 2),
                        number_format($interest, 2),
                        number_format($fees, 2),
                        number_format($penalty, 2),
                        number_format($due, 2),
                        number_format($balance, 2),
                        number_format($days_arrears, 2),
                        number_format($days_last_payment, 2),
                    ]);
                }

            }
            array_push($data, [
                "",
                "",
                "",
                "",
                "",
                number_format($total_amount, 2),
                "",
                "",
                number_format($total_principal, 2),
                number_format($total_interest, 2),
                number_format($total_fees, 2),
                number_format($total_penalty, 2),
                number_format($total_outstanding, 2),
                number_format($total_due, 2),
                "",
                "",
            ]);

            Excel::create(trans_choice('general.arrears', 1) . ' ' . trans_choice('general.report',
                    2),
                function ($excel) use ($data) {
                    $excel->sheet('Sheet', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', false, false);
                        $sheet->mergeCells('A1:P1');
                    });

                })->download('csv');
        }


    }

    public function disbursed_loans(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $user_id = $request->user_id;
        $loan_product_id = $request->loan_product_id;
        $users = [];
        $data = [];
        $users["-1"] = trans_choice('general.all', 1);
        foreach (User::all() as $key) {
            $users[$key->id] = $key->first_name . ' ' . $key->last_name;
        }
        $users = [];
        $users["-1"] = trans_choice('general.all', 1);
        foreach (User::all() as $key) {
            $users[$key->id] = $key->first_name . ' ' . $key->last_name;
        }
        $loan_products = [];
        $loan_products["-1"] = trans_choice('general.all', 1);
        foreach (LoanProduct::all() as $key) {
            $loan_products[$key->id] = $key->name;
        }
        if (!empty($start_date)) {
            //get disbursed loans within specified period and officer
            if ($request->loan_product_id == "-1") {
                $data = Loan::whereIn('status',
                    ['disbursed', 'closed', 'written_off'])->where('branch_id',
                    session('branch_id'))->whereBetween('release_date',
                    [$start_date, $end_date])->get();
            } else {
                $data = Loan::where('loan_product_id', $request->loan_product_id)->where('branch_id',
                    session('branch_id'))->whereIn('status',
                    ['disbursed', 'closed', 'written_off'])->whereBetween('release_date',
                    [$start_date, $end_date])->get();
            }
        }

        return view('loan_report.disbursed_loans',
            compact('start_date',
                'end_date', 'data', 'user_id', 'loan_product_id', 'users', 'loan_products'));
    }
//Reporte de prestamos desembolsados
    public function disbursed_loans_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
//        $user_id = $request->user_id;
        $loan_product_id = $request->loan_product_id;
        if (!empty($end_date)) {
            if ($request->loan_product_id == "-1") {
                $ldata = Loan::whereIn('status',
                    ['disbursed', 'closed', 'written_off'])->where('branch_id',
                    session('branch_id'))->whereBetween('release_date',
                    [$start_date, $end_date])->get();
            } else {
                $ldata = Loan::where('loan_product_id', $request->loan_product_id)->where('branch_id',
                    session('branch_id'))->whereIn('status',
                    ['disbursed', 'closed', 'written_off'])->whereBetween('release_date',
                    [$start_date, $end_date])->get();
            }
            $data = [];
            array_push($data, [
                trans_choice('general.disbursed', 1) . ' ' . trans_choice('general.loan',
                    2)
            ]);
            array_push($data, [
                trans_choice('general.id', 1),
                trans_choice('general.product', 1),
                trans_choice('general.disbursed', 1),
                trans_choice('general.maturity', 1) . " " . trans_choice('general.date',
                    1),
                trans_choice('general.principal', 1),
                trans_choice('general.interest', 1),
                trans_choice('general.fee', 2),
                trans_choice('general.penalty', 1),
                trans_choice('general.total', 1),
                trans_choice('general.payment', 2),
                trans_choice('general.balance', 1),
            ]);
            $total_outstanding = 0;
            $total_due = 0;
            $total_payments = 0;
            $total_principal = 0;
            $total_interest = 0;
            $total_fees = 0;
            $total_penalty = 0;
            $total_amount = 0;
            foreach ($ldata as $key) {
                $loan_due_items = \App\Helpers\GeneralHelper::loan_due_items($key->id);
                $loan_paid_items = \App\Helpers\GeneralHelper::loan_paid_items($key->id);
                $due = $loan_due_items["principal"] + $loan_due_items["interest"] + $loan_due_items["fees"] + $loan_due_items["penalty"];
                $payments = $loan_paid_items["principal"] + $loan_paid_items["interest"] + $loan_paid_items["fees"] + $loan_paid_items["penalty"];
                $balance = $due - $payments;
                $principal = $loan_due_items["principal"];
                $interest = $loan_due_items["interest"];
                $fees = $loan_due_items["fees"];
                $penalty = $loan_due_items["penalty"];

                $total_outstanding = $total_outstanding + $balance;
                $total_due = $total_due + $due;
                $total_principal = $total_principal + $principal;
                $total_interest = $total_interest + $interest;
                $total_fees = $total_fees + $fees;
                $total_penalty = $total_penalty + $penalty;
                $total_payments = $total_payments + $payments;
                if (!empty($key->borrower)) {
                    $borrower = $key->borrower->first_name . " " . $key->borrower->last_name;
                    $borrower_phone = $key->borrower->mobile;
                } else {
                    $borrower = "";
                    $borrower_phone = "";
                }
                if (!empty($key->loan_officer)) {
                    $loan_officer = $key->loan_officer->first_name . " " . $key->loan_officer->last_name;
                } else {
                    $loan_officer = "";
                }
                if (!empty($key->loan_product)) {
                    $loan_product = $key->loan_product->name;
            } else {
                    $loan_product = "";
                }
                if ($due > 0) {
                    array_push($data, [
                        $key->id,
                        $borrower,
                        $loan_product,
                       $key->release_date,
                        $key->maturity_date,
                        number_format($principal, 2),
                        number_format($interest, 2),
                        number_format($fees, 2),
                        number_format($penalty, 2),
                        number_format($due, 2),
                        number_format($payments, 2),
                    number_format($balance, 2),
                    ]);
                }

            }
            array_push($data, [
                "",
                "",
                "",
                "",
                number_format($total_principal, 2),
                number_format($total_interest, 2),
                number_format($total_fees, 2),
                number_format($total_penalty, 2),
                number_format($total_due, 2),
                number_format($total_payments, 2),
                number_format($total_outstanding, 2),
            ]);

            Excel::create(trans_choice('general.disbursed', 1) . ' ' . trans_choice('general.loan',
                    2),
                function ($excel) use ($data) {
                    $excel->sheet('Sheet', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', false, false);
                        $sheet->mergeCells('A1:K1');
                    });

                })->download('xls');
        }


    }

    public function disbursed_loans_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $user_id = $request->user_id;
        $loan_product_id = $request->loan_product_id;
        if (!empty($end_date)) {
            if ($request->loan_product_id == "-1") {
                $ldata = Loan::where('status', 'disbursed')->where('branch_id',
                    session('branch_id'))->whereBetween('release_date',
                    [$start_date, $end_date])->get();
            } else {
                $ldata = Loan::where('loan_product_id', $request->loan_product_id)->where('branch_id',
                    session('branch_id'))->where('status', 'disbursed')->whereBetween('release_date',
                    [$start_date, $end_date])->get();
            }
            $data = [];
            array_push($data, [
                trans_choice('general.disbursed', 1) . ' ' . trans_choice('general.loan',
                    2)
            ]);
            array_push($data, [
                trans_choice('general.id', 1),
                trans_choice('general.product', 1),
                trans_choice('general.disbursed', 1),
                trans_choice('general.maturity', 1) . " " . trans_choice('general.date',
                    1),
                trans_choice('general.principal', 1),
                trans_choice('general.interest', 1),
                trans_choice('general.fee', 2),
                trans_choice('general.penalty', 1),
                trans_choice('general.total', 1),
                trans_choice('general.payment', 2),
                trans_choice('general.balance', 1),
            ]);
            $total_outstanding = 0;
            $total_due = 0;
            $total_payments = 0;
            $total_principal = 0;
            $total_interest = 0;
            $total_fees = 0;
            $total_penalty = 0;
            $total_amount = 0;
            foreach ($ldata as $key) {
                $loan_due_items = \App\Helpers\GeneralHelper::loan_due_items($key->id);
                $loan_paid_items = \App\Helpers\GeneralHelper::loan_paid_items($key->id);
                $due = $loan_due_items["principal"] + $loan_due_items["interest"] + $loan_due_items["fees"] + $loan_due_items["penalty"];
                $payments = $loan_paid_items["principal"] + $loan_paid_items["interest"] + $loan_paid_items["fees"] + $loan_paid_items["penalty"];
                $balance = $due - $payments;
                $principal = $loan_due_items["principal"];
                $interest = $loan_due_items["interest"];
                $fees = $loan_due_items["fees"];
                $penalty = $loan_due_items["penalty"];

                $total_outstanding = $total_outstanding + $balance;
                $total_due = $total_due + $due;
                $total_principal = $total_principal + $principal;
                $total_interest = $total_interest + $interest;
                $total_fees = $total_fees + $fees;
                $total_penalty = $total_penalty + $penalty;
                $total_payments = $total_payments + $payments;
                if (!empty($key->borrower)) {
                    $borrower = $key->borrower->first_name . " " . $key->borrower->last_name;
                    $borrower_phone = $key->borrower->mobile;
                } else {
                    $borrower = "";
                    $borrower_phone = "";
                }
                if (!empty($key->loan_officer)) {
                    $loan_officer = $key->loan_officer->first_name . " " . $key->loan_officer->last_name;
                } else {
                    $loan_officer = "";
                }
                if (!empty($key->loan_product)) {
                    $loan_product = $key->loan_product->name;
                } else {
                    $loan_product = "";
                }
                if ($due > 0) {
                    array_push($data, [
                        $key->id,
                        $borrower,
                        $loan_product,
                        $key->release_date,
                        $key->maturity_date,
                        number_format($principal, 2),
                        number_format($interest, 2),
                        number_format($fees, 2),
                        number_format($penalty, 2),
                        number_format($due, 2),
                        number_format($payments, 2),
                        number_format($balance, 2),
                    ]);
                }

            }
            array_push($data, [
                "",
                "",
                "",
                "",
                number_format($total_principal, 2),
                number_format($total_interest, 2),
                number_format($total_fees, 2),
                number_format($total_penalty, 2),
                number_format($total_due, 2),
                number_format($total_payments, 2),
                number_format($total_outstanding, 2),
            ]);

            Excel::create(trans_choice('general.disbursed', 1) . ' ' . trans_choice('general.loan',
                    2),
                function ($excel) use ($data) {
                    $excel->sheet('Sheet', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', false, false);
                        $sheet->mergeCells('A1:K1');
                    });

                })->download('csv');
        }
    }

    public function borrower_numbers(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }

        if (isset($request->start_date)) {
            $start_date = $request->start_date;
        } else {
            $start_date = '';
        }

        if (isset($request->end_date)) {
            $end_date = $request->end_date;
        } else {
            $end_date = '';
        }

        return view('borrower_report.borrower_numbers',
            compact('start_date',
                'end_date'));
    }

    public function borrower_numbers_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        if (!empty($end_date)) {
            $pdf = PDF::loadView('borrower_report.borrower_numbers_pdf', compact('start_date',
                'end_date', 'data'));
            //$pdf->setPaper('A4', 'landscape');
            return $pdf->download(trans_choice('general.borrower', 1) . ' ' . trans_choice('general.number',
                    2) . ".pdf");
        }

    }

    public function borrower_numbers_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($end_date)) {

            $data = [];
            array_push($data, [
                trans_choice('general.borrower', 1) . ' ' . trans_choice('general.number',
                    2)
            ]);
            array_push($data, [
                trans_choice('general.name', 1),
                trans_choice('general.value', 1),
            ]);
            $total_borrowers = 0;
            $blacklisted_borrowers = 0;
            $dormant_borrowers = 0;
            $active_borrowers = 0;
            $new_borrowers = 0;
            foreach (Borrower::all() as $key) {
                $total_borrowers = $total_borrowers + 1;
                if ($key->blacklisted == 1) {
                    $blacklisted_borrowers = $blacklisted_borrowers + 1;
                }
                if ($start_date <= date_format(date_create($key->created_at),
                        "Y-m-d ") && $end_date >= date_format(date_create($key->created_at), "Y-m-d ")
                ) {
                    $new_borrowers = $new_borrowers + 1;
                }
                if (count($key->loans) > 0) {
                    $active_borrowers = $active_borrowers + 1;
                } else {
                    $dormant_borrowers = $dormant_borrowers + 1;
                }
            }
            array_push($data, [
                trans_choice('general.dormant', 1) . " " . trans_choice('general.borrower', 2),
                $total_borrowers,
            ]);
            array_push($data, [
                trans_choice('general.new', 1) . " " . trans_choice('general.borrower', 2),
                $new_borrowers,
            ]);
            array_push($data, [
                trans_choice('general.blacklisted', 1) . " " . trans_choice('general.borrower', 2),
                $blacklisted_borrowers,
            ]);
            array_push($data, [
                trans_choice('general.total', 1) . " " . trans_choice('general.borrower', 2),
                $total_borrowers,
            ]);

            Excel::create(trans_choice('general.arrears', 1) . ' ' . trans_choice('general.report',
                    2),
                function ($excel) use ($data) {
                    $excel->sheet('Sheet', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', false, false);
                        $sheet->mergeCells('A1:B1');
                    });

                })->download('xls');
        }


    }

    public function borrower_numbers_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($end_date)) {

            $data = [];
            array_push($data, [
                trans_choice('general.borrower', 1) . ' ' . trans_choice('general.number',
                    2)
            ]);
            array_push($data, [
                trans_choice('general.name', 1),
                trans_choice('general.value', 1),
            ]);
            $total_borrowers = 0;
            $blacklisted_borrowers = 0;
            $dormant_borrowers = 0;
            $active_borrowers = 0;
            $new_borrowers = 0;
            foreach (Borrower::all() as $key) {
                $total_borrowers = $total_borrowers + 1;
                if ($key->blacklisted == 1) {
                    $blacklisted_borrowers = $blacklisted_borrowers + 1;
                }
                if ($start_date <= date_format(date_create($key->created_at),
                        "Y-m-d ") && $end_date >= date_format(date_create($key->created_at), "Y-m-d ")
                ) {
                    $new_borrowers = $new_borrowers + 1;
                }
                if (count($key->loans) > 0) {
                    $active_borrowers = $active_borrowers + 1;
                } else {
                    $dormant_borrowers = $dormant_borrowers + 1;
                }
            }
            array_push($data, [
                trans_choice('general.dormant', 1) . " " . trans_choice('general.borrower', 2),
                $total_borrowers,
            ]);
            array_push($data, [
                trans_choice('general.new', 1) . " " . trans_choice('general.borrower', 2),
                $new_borrowers,
            ]);
            array_push($data, [
                trans_choice('general.blacklisted', 1) . " " . trans_choice('general.borrower', 2),
                $blacklisted_borrowers,
            ]);
            array_push($data, [
                trans_choice('general.total', 1) . " " . trans_choice('general.borrower', 2),
                $total_borrowers,
            ]);

            Excel::create(trans_choice('general.arrears', 1) . ' ' . trans_choice('general.report',
                    2),
                function ($excel) use ($data) {
                    $excel->sheet('Sheet', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', false, false);
                        $sheet->mergeCells('A1:B1');
                    });

                })->download('csv');
        }


    }

    public function provisioning(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;


        return view('financial_report.provisioning',
            compact('start_date',
                'end_date', 'data'));
    }

    public function provisioning_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        if (!empty($end_date)) {
            $pdf = PDF::loadView('financial_report.provisioning_pdf', compact('start_date',
                'end_date', 'data'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download(trans_choice('general.provisioning', 1) . ' ' . trans_choice('general.report',
                    2) . ".pdf");
        }

    }

    public function provisioning_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($end_date)) {

            $data = [];
            array_push($data, [
                trans_choice('general.provisioning', 1) . ' ' . trans_choice('general.report',
                    2)
            ]);
            array_push($data, [
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                trans_choice('general.outstanding', 1),
                "",
                "",
                "",
                "",
                trans_choice('general.arrears', 1),
                "",
                trans_choice('general.provisioning', 1),
                "",
                "",
            ]);
            array_push($data, [
                trans_choice('general.loan_officer', 1),
                trans_choice('general.borrower', 1),
                trans_choice('general.loan', 1) . " " . trans_choice('general.id',
                    1),
                trans_choice('general.product', 1),
                trans_choice('general.amount', 1),
                trans_choice('general.disbursed', 1),
                trans_choice('general.maturity', 1) . " " . trans_choice('general.date',
                    1),
                trans_choice('general.principal', 1),
                trans_choice('general.interest', 1),
                trans_choice('general.fee', 2),
                trans_choice('general.penalty', 2),
                trans_choice('general.total', 1),
                trans_choice('general.day', 2),
                trans_choice('general.amount', 1),
                trans_choice('general.percentage', 1),
                trans_choice('general.amount', 1),
                trans_choice('general.classification', 1),
            ]);
            $total_outstanding = 0;
            $total_arrears = 0;
            $total_principal = 0;
            $total_interest = 0;
            $total_fees = 0;
            $total_penalty = 0;
            $total_provisioning_amount = 0;
            $total_amount = 0;
            foreach (Loan::where('release_date', '<=', $end_date)->where('branch_id',
                session('branch_id'))->where('status', 'disbursed')->orderBy('release_date', 'asc')->get() as $key) {
                $loan_due_items = \App\Helpers\GeneralHelper::loan_due_items($key->id);
                $loan_due_items_arrears = \App\Helpers\GeneralHelper::loan_due_items($key->id,
                    $key->release_date, $end_date);
                $loan_paid_items = \App\Helpers\GeneralHelper::loan_paid_items($key->id,
                    $key->release_date, $end_date);
                $due = ($loan_due_items["principal"] + $loan_due_items["interest"] + $loan_due_items["fees"] + $loan_due_items["penalty"]) - ($loan_paid_items["principal"] + $loan_paid_items["interest"] + $loan_paid_items["fees"] + $loan_paid_items["penalty"]);
                $principal = $loan_due_items["principal"] - $loan_paid_items["principal"];
                $interest = $loan_due_items["interest"] - $loan_paid_items["interest"];
                $fees = $loan_due_items["fees"] - $loan_paid_items["fees"];
                $penalty = $loan_due_items["penalty"] - $loan_paid_items["penalty"];
                $arrears = ($loan_due_items_arrears["principal"] + $loan_due_items_arrears["interest"] + $loan_due_items_arrears["fees"] + $loan_due_items_arrears["penalty"]) - ($loan_paid_items["principal"] + $loan_paid_items["interest"] + $loan_paid_items["fees"] + $loan_paid_items["penalty"]);
                $total_outstanding = $total_outstanding + $due;
                $total_arrears = $total_arrears + $arrears;
                $total_principal = $total_principal + $principal;
                $total_interest = $total_interest + $interest;
                $total_fees = $total_fees + $fees;
                $total_penalty = $total_penalty + $penalty;
                $total_amount = $total_amount + $key->principal;

                if (!empty($key->borrower)) {
                    $borrower = $key->borrower->first_name . " " . $key->borrower->last_name;
                    $borrower_phone = $key->borrower->mobile;
                } else {
                    $borrower = "";
                    $borrower_phone = "";
                }
                if (!empty($key->loan_officer)) {
                    $loan_officer = $key->loan_officer->first_name . " " . $key->loan_officer->last_name;
                } else {
                    $loan_officer = "";
                }
                if (!empty($key->loan_product)) {
                    $loan_product = $key->loan_product->name;
                } else {
                    $loan_product = "";
                }

                if ($due > 0) {
                    //lets find arrears information
                    $schedules = LoanSchedule::where('loan_id', $key->id)->where('due_date', '<=',
                        $end_date)->orderBy('due_date', 'asc')->get();
                    $payments = $loan_paid_items["principal"] + $loan_paid_items["interest"] + $loan_paid_items["fees"] + $loan_paid_items["penalty"];
                    if ($payments > 0) {
                        foreach ($schedules as $schedule) {
                            if ($payments > $schedule->principal + $schedule->interest + $schedule->penalty + $schedule->fees) {
                                $payments = $payments - ($schedule->principal + $schedule->interest + $schedule->penalty + $schedule->fees);
                            } else {
                                $payments = 0;
                                $overdue_date = $schedule->due_date;
                                break;
                            }
                        }
                    } else {
                        $overdue_date = $schedules->first()->due_date;
                    }
                    $date1 = new \DateTime($overdue_date);
                    $date2 = new \DateTime($end_date);
                    $days_arrears = $date2->diff($date1)->format("%a");
                    $transaction = LoanTransaction::where('loan_id',
                        $key->id)->where('transaction_type',
                        'repayment')->where('reversed', 0)->orderBy('date', 'desc')->first();
                    if (!empty($transaction)) {
                        $date2 = new \DateTime($transaction->date);
                        $date1 = new \DateTime($end_date);
                        $days_last_payment = $date2->diff($date1)->format("%r%a");
                    } else {
                        $days_last_payment = 0;
                    }
                } else {
                    $days_arrears = 0;
                }
                //find the classification
                if ($days_arrears < 30) {
                    $classification = trans_choice('general.current', 1);
                    $provision_rate = ProvisionRate::find(1)->rate;
                    $provision = $provision_rate * $principal / 100;
                    $total_provisioning_amount = $total_provisioning_amount + $provision;
                } elseif ($days_arrears > 30 && $days_arrears < 61) {
                    $classification = trans_choice('general.especially_mentioned', 1);
                    $provision_rate = ProvisionRate::find(2)->rate;
                    $provision = $provision_rate * $principal / 100;
                    $total_provisioning_amount = $total_provisioning_amount + $provision;
                } elseif ($days_arrears > 60 && $days_arrears < 91) {
                    $classification = trans_choice('general.substandard', 1);
                    $provision_rate = ProvisionRate::find(3)->rate;
                    $provision = $provision_rate * $principal / 100;
                    $total_provisioning_amount = $total_provisioning_amount + $provision;
                } elseif ($days_arrears > 90 && $days_arrears < 181) {
                    $classification = trans_choice('general.doubtful', 1);
                    $provision_rate = ProvisionRate::find(4)->rate;
                    $provision = $provision_rate * $principal / 100;
                    $total_provisioning_amount = $total_provisioning_amount + $provision;
                } elseif ($days_arrears > 180) {
                    $classification = trans_choice('general.loss', 1);
                    $provision_rate = ProvisionRate::find(5)->rate;
                    $provision = $provision_rate * $principal / 100;
                    $total_provisioning_amount = $total_provisioning_amount + $provision;
                }
                array_push($data, [
                    $loan_officer,
                    $borrower,
                    $key->id,
                    $loan_product,
                    $key->principal,
                    $key->release_date,
                    $key->maturity_date,
                    number_format($principal, 2),
                    number_format($interest, 2),
                    number_format($fees, 2),
                    number_format($penalty, 2),
                    number_format($due, 2),
                    $days_arrears,
                    number_format($arrears, 2),
                    number_format($provision_rate, 2),
                    number_format($provision, 2),
                    $classification,
                ]);
            }
            array_push($data, [
                "",
                "",
                "",
                "",
                number_format($total_amount, 2),
                "",
                "",
                number_format($total_principal, 2),
                number_format($total_interest, 2),
                number_format($total_fees, 2),
                number_format($total_penalty, 2),
                number_format($total_outstanding, 2),
                "",
                number_format($total_arrears, 2),
                "",
                number_format($total_provisioning_amount, 2),
                "",
            ]);

            Excel::create(trans_choice('general.provisioning', 1) . ' ' . trans_choice('general.report',
                    2),
                function ($excel) use ($data) {
                    $excel->sheet('Sheet', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', false, false);
                        $sheet->mergeCells('A1:Q1');
                    });

                })->download('xls');
        }


    }

    public function provisioning_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($end_date)) {

            $data = [];
            array_push($data, [
                trans_choice('general.provisioning', 1) . ' ' . trans_choice('general.report',
                    2)
            ]);
            array_push($data, [
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                trans_choice('general.outstanding', 1),
                "",
                "",
                "",
                "",
                trans_choice('general.arrears', 1),
                "",
                trans_choice('general.provisioning', 1),
                "",
                "",
            ]);
            array_push($data, [
                trans_choice('general.loan_officer', 1),
                trans_choice('general.borrower', 1),
                trans_choice('general.loan', 1) . " " . trans_choice('general.id',
                    1),
                trans_choice('general.product', 1),
                trans_choice('general.amount', 1),
                trans_choice('general.disbursed', 1),
                trans_choice('general.maturity', 1) . " " . trans_choice('general.date',
                    1),
                trans_choice('general.principal', 1),
                trans_choice('general.interest', 1),
                trans_choice('general.fee', 2),
                trans_choice('general.penalty', 2),
                trans_choice('general.total', 1),
                trans_choice('general.day', 2),
                trans_choice('general.amount', 1),
                trans_choice('general.percentage', 1),
                trans_choice('general.amount', 1),
                trans_choice('general.classification', 1),
            ]);
            $total_outstanding = 0;
            $total_arrears = 0;
            $total_principal = 0;
            $total_interest = 0;
            $total_fees = 0;
            $total_penalty = 0;
            $total_provisioning_amount = 0;
            $total_amount = 0;
            foreach (Loan::where('release_date', '<=', $end_date)->where('branch_id',
                session('branch_id'))->where('status', 'disbursed')->orderBy('release_date', 'asc')->get() as $key) {
                $loan_due_items = \App\Helpers\GeneralHelper::loan_due_items($key->id);
                $loan_due_items_arrears = \App\Helpers\GeneralHelper::loan_due_items($key->id,
                    $key->release_date, $end_date);
                $loan_paid_items = \App\Helpers\GeneralHelper::loan_paid_items($key->id,
                    $key->release_date, $end_date);
                $due = ($loan_due_items["principal"] + $loan_due_items["interest"] + $loan_due_items["fees"] + $loan_due_items["penalty"]) - ($loan_paid_items["principal"] + $loan_paid_items["interest"] + $loan_paid_items["fees"] + $loan_paid_items["penalty"]);
                $principal = $loan_due_items["principal"] - $loan_paid_items["principal"];
                $interest = $loan_due_items["interest"] - $loan_paid_items["interest"];
                $fees = $loan_due_items["fees"] - $loan_paid_items["fees"];
                $penalty = $loan_due_items["penalty"] - $loan_paid_items["penalty"];
                $arrears = ($loan_due_items_arrears["principal"] + $loan_due_items_arrears["interest"] + $loan_due_items_arrears["fees"] + $loan_due_items_arrears["penalty"]) - ($loan_paid_items["principal"] + $loan_paid_items["interest"] + $loan_paid_items["fees"] + $loan_paid_items["penalty"]);
                $total_outstanding = $total_outstanding + $due;
                $total_arrears = $total_arrears + $arrears;
                $total_principal = $total_principal + $principal;
                $total_interest = $total_interest + $interest;
                $total_fees = $total_fees + $fees;
                $total_penalty = $total_penalty + $penalty;
                $total_amount = $total_amount + $key->principal;

                if (!empty($key->borrower)) {
                    $borrower = $key->borrower->first_name . " " . $key->borrower->last_name;
                    $borrower_phone = $key->borrower->mobile;
                } else {
                    $borrower = "";
                    $borrower_phone = "";
                }
                if (!empty($key->loan_officer)) {
                    $loan_officer = $key->loan_officer->first_name . " " . $key->loan_officer->last_name;
                } else {
                    $loan_officer = "";
                }
                if (!empty($key->loan_product)) {
                    $loan_product = $key->loan_product->name;
                } else {
                    $loan_product = "";
                }

                if ($due > 0) {
                    //lets find arrears information
                    $schedules = LoanSchedule::where('loan_id', $key->id)->where('due_date', '<=',
                        $end_date)->orderBy('due_date', 'asc')->get();
                    $payments = $loan_paid_items["principal"] + $loan_paid_items["interest"] + $loan_paid_items["fees"] + $loan_paid_items["penalty"];
                    if ($payments > 0) {
                        foreach ($schedules as $schedule) {
                            if ($payments > $schedule->principal + $schedule->interest + $schedule->penalty + $schedule->fees) {
                                $payments = $payments - ($schedule->principal + $schedule->interest + $schedule->penalty + $schedule->fees);
                            } else {
                                $payments = 0;
                                $overdue_date = $schedule->due_date;
                                break;
                            }
                        }
                    } else {
                        $overdue_date = $schedules->first()->due_date;
                    }
                    $date1 = new \DateTime($overdue_date);
                    $date2 = new \DateTime($end_date);
                    $days_arrears = $date2->diff($date1)->format("%a");
                    $transaction = LoanTransaction::where('loan_id',
                        $key->id)->where('transaction_type',
                        'repayment')->where('reversed', 0)->orderBy('date', 'desc')->first();
                    if (!empty($transaction)) {
                        $date2 = new \DateTime($transaction->date);
                        $date1 = new \DateTime($end_date);
                        $days_last_payment = $date2->diff($date1)->format("%r%a");
                    } else {
                        $days_last_payment = 0;
                    }
                } else {
                    $days_arrears = 0;
                }
                //find the classification
                if ($days_arrears < 30) {
                    $classification = trans_choice('general.current', 1);
                    $provision_rate = ProvisionRate::find(1)->rate;
                    $provision = $provision_rate * $principal / 100;
                    $total_provisioning_amount = $total_provisioning_amount + $provision;
                } elseif ($days_arrears > 30 && $days_arrears < 61) {
                    $classification = trans_choice('general.especially_mentioned', 1);
                    $provision_rate = ProvisionRate::find(2)->rate;
                    $provision = $provision_rate * $principal / 100;
                    $total_provisioning_amount = $total_provisioning_amount + $provision;
                } elseif ($days_arrears > 60 && $days_arrears < 91) {
                    $classification = trans_choice('general.substandard', 1);
                    $provision_rate = ProvisionRate::find(3)->rate;
                    $provision = $provision_rate * $principal / 100;
                    $total_provisioning_amount = $total_provisioning_amount + $provision;
                } elseif ($days_arrears > 90 && $days_arrears < 181) {
                    $classification = trans_choice('general.doubtful', 1);
                    $provision_rate = ProvisionRate::find(4)->rate;
                    $provision = $provision_rate * $principal / 100;
                    $total_provisioning_amount = $total_provisioning_amount + $provision;
                } elseif ($days_arrears > 180) {
                    $classification = trans_choice('general.loss', 1);
                    $provision_rate = ProvisionRate::find(5)->rate;
                    $provision = $provision_rate * $principal / 100;
                    $total_provisioning_amount = $total_provisioning_amount + $provision;
                }
                array_push($data, [
                    $loan_officer,
                    $borrower,
                    $key->id,
                    $loan_product,
                    $key->principal,
                    $key->release_date,
                    $key->maturity_date,
                    number_format($principal, 2),
                    number_format($interest, 2),
                    number_format($fees, 2),
                    number_format($penalty, 2),
                    number_format($due, 2),
                    $days_arrears,
                    number_format($arrears, 2),
                    number_format($provision_rate, 2),
                    number_format($provision, 2),
                    $classification,
                ]);
            }
            array_push($data, [
                "",
                "",
                "",
                "",
                number_format($total_amount, 2),
                "",
                "",
                number_format($total_principal, 2),
                number_format($total_interest, 2),
                number_format($total_fees, 2),
                number_format($total_penalty, 2),
                number_format($total_outstanding, 2),
                "",
                number_format($total_arrears, 2),
                "",
                number_format($total_provisioning_amount, 2),
                "",
            ]);

            Excel::create(trans_choice('general.provisioning', 1) . ' ' . trans_choice('general.report',
                    2),
                function ($excel) use ($data) {
                    $excel->sheet('Sheet', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', false, false);
                        $sheet->mergeCells('A1:Q1');
                    });

                })->download('csv');
        }


    }
//	Reporte Distribucion General
    public function products_summary(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;


        return view('company_report.products_summary',
            compact('start_date',
                'end_date'));
    }

    public function products_summary_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        if (!empty($end_date)) {
            $pdf = PDF::loadView('company_report.products_summary_pdf', compact('start_date',
                'end_date'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download(trans_choice('general.product', 2) . ' ' . trans_choice('general.summary',
                    1) . ".pdf");
        }

    }

    public function products_summary_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($end_date)) {

            $data = [];
            array_push($data, [
                trans_choice('general.product', 2) . ' ' . trans_choice('general.summary',
                    1)
            ]);
            array_push($data, [
                "",
                trans_choice('general.total', 1) . " " . trans_choice('general.disbursed', 1),
                "",
                "",
                "",
                "",
                trans_choice('general.outstanding', 1),
                "",
                "",
                "",
                "",
            ]);
            array_push($data, [
                trans_choice('general.name', 1),
                trans_choice('general.loan', 2),
                trans_choice('general.principal', 1),
                trans_choice('general.interest', 1),
                trans_choice('general.fee', 2),
                trans_choice('general.total', 1),
                trans_choice('general.principal', 1),
                trans_choice('general.interest', 1),
                trans_choice('general.fee', 2),
                trans_choice('general.penalty', 2),
                trans_choice('general.total', 1),
            ]);
            $total_disbursed = 0;
            $total_disbursed_loans = 0;
            $total_disbursed_principal = 0;
            $total_disbursed_interest = 0;
            $total_disbursed_fees = 0;
            $total_disbursed_penalty = 0;
            $total_outstanding = 0;
            $total_outstanding_principal = 0;
            $total_outstanding_interest = 0;
            $total_outstanding_fees = 0;
            $total_outstanding_penalty = 0;
            foreach (LoanProduct::get() as $key) {
                $principal_disbursed = 0;
                $interest_disbursed = 0;
                $fees_disbursed = 0;
                $penalty_disbursed = 0;
                $principal_outstanding = 0;
                $interest_outstanding = 0;
                $fees_outstanding = 0;
                $penalty_outstanding = 0;
                $disbursed_loans = 0;
                $disbursed = 0;
                $outstanding = 0;
                //loop through loans, this will need to be improved
                foreach (Loan::where('loan_product_id', $key->id)->where('branch_id',
                    session('branch_id'))->whereIn('status',
                    ['disbursed', 'closed', 'written_off'])->whereBetween('release_date',
                    [$start_date, $end_date])->get() as $loan) {
                    $disbursed_loans = $disbursed_loans + 1;
                    $loan_due_items = \App\Helpers\GeneralHelper::loan_due_items($key->id);
                    $loan_paid_items = \App\Helpers\GeneralHelper::loan_paid_items($key->id);
                    $principal_disbursed = $principal_disbursed + $loan_due_items["principal"];
                    $interest_disbursed = $interest_disbursed + $loan_due_items["interest"];
                    $fees_disbursed = $fees_disbursed + $loan_due_items["fees"];
                    $penalty_disbursed = $penalty_disbursed + $loan_due_items["penalty"];
                    $principal_outstanding = $principal_outstanding + $loan_due_items["principal"] - $loan_paid_items["principal"];
                    $interest_outstanding = $interest_outstanding + $loan_due_items["interest"] - $loan_paid_items["interest"];
                    $fees_outstanding = $fees_outstanding + $loan_due_items["fees"] - $loan_paid_items["fees"];
                    $penalty_outstanding = $penalty_outstanding + $loan_due_items["penalty"] - $loan_paid_items["penalty"];
                }
                $disbursed = $principal_disbursed + $interest_disbursed + $fees_disbursed;
                $outstanding = $principal_outstanding + $interest_outstanding + $fees_outstanding + $penalty_outstanding;
                $total_disbursed = $total_disbursed + $disbursed;
                $total_disbursed_loans = $total_disbursed_loans + $disbursed_loans;
                $total_disbursed_principal = $total_disbursed_principal + $principal_disbursed;
                $total_disbursed_interest = $total_disbursed_interest + $interest_disbursed;
                $total_disbursed_fees = $total_disbursed_fees + $fees_disbursed;
                $total_disbursed_penalty = $total_disbursed_penalty + $penalty_disbursed;
                $total_outstanding_principal = $total_outstanding_principal + $principal_outstanding;
                $total_outstanding_interest = $total_outstanding_interest + $interest_outstanding;
                $total_outstanding_fees = $total_outstanding_fees + $fees_outstanding;
                $total_outstanding_penalty = $total_outstanding_penalty + $penalty_outstanding;
                $total_outstanding = $total_outstanding + $principal_outstanding + $interest_outstanding + $fees_outstanding + $penalty_outstanding;

                array_push($data, [
                    $key->name,
                    $disbursed_loans,
                    number_format($principal_disbursed, 2),
                    number_format($interest_disbursed, 2),
                    number_format($fees_disbursed, 2),
                    number_format($disbursed, 2),
                    number_format($principal_outstanding, 2),
                    number_format($interest_outstanding, 2),
                    number_format($fees_outstanding, 2),
                    number_format($penalty_outstanding, 2),
                    number_format($outstanding, 2),
                ]);
            }
            array_push($data, [
                "",
                $total_disbursed_loans,
                number_format($total_disbursed_principal, 2),
                number_format($total_disbursed_interest, 2),
                number_format($total_disbursed_fees, 2),
                number_format($total_disbursed, 2),
                number_format($total_outstanding_principal, 2),
                number_format($total_outstanding_interest, 2),
                number_format($total_outstanding_fees, 2),
                number_format($total_outstanding_penalty, 2),
                number_format($total_outstanding, 2),
            ]);

            Excel::create(trans_choice('general.product', 2) . ' ' . trans_choice('general.summary',
                    1),
                function ($excel) use ($data) {
                    $excel->sheet('Sheet', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', false, false);
                        $sheet->mergeCells('A1:K1');
                    });

                })->download('xls');
        }


    }

    public function products_summary_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($end_date)) {

            $data = [];
            array_push($data, [
                trans_choice('general.product', 2) . ' ' . trans_choice('general.summary',
                    1)
            ]);
            array_push($data, [
                "",
                trans_choice('general.total', 1) . " " . trans_choice('general.disbursed', 1),
                "",
                "",
                "",
                "",
                trans_choice('general.outstanding', 1),
                "",
                "",
                "",
                "",
            ]);
            array_push($data, [
                trans_choice('general.name', 1),
                trans_choice('general.loan', 2),
                trans_choice('general.principal', 1),
                trans_choice('general.interest', 1),
                trans_choice('general.fee', 2),
                trans_choice('general.total', 1),
                trans_choice('general.principal', 1),
                trans_choice('general.interest', 1),
                trans_choice('general.fee', 2),
                trans_choice('general.penalty', 2),
                trans_choice('general.total', 1),
            ]);
            $total_disbursed = 0;
            $total_disbursed_loans = 0;
            $total_disbursed_principal = 0;
            $total_disbursed_interest = 0;
            $total_disbursed_fees = 0;
            $total_disbursed_penalty = 0;
            $total_outstanding = 0;
            $total_outstanding_principal = 0;
            $total_outstanding_interest = 0;
            $total_outstanding_fees = 0;
            $total_outstanding_penalty = 0;
            foreach (LoanProduct::get() as $key) {
                $principal_disbursed = 0;
                $interest_disbursed = 0;
                $fees_disbursed = 0;
                $penalty_disbursed = 0;
                $principal_outstanding = 0;
                $interest_outstanding = 0;
                $fees_outstanding = 0;
                $penalty_outstanding = 0;
                $disbursed_loans = 0;
                $disbursed = 0;
                $outstanding = 0;
                //loop through loans, this will need to be improved
                foreach (Loan::where('loan_product_id', $key->id)->where('branch_id',
                    session('branch_id'))->whereIn('status',
                    ['disbursed', 'closed', 'written_off'])->whereBetween('release_date',
                    [$start_date, $end_date])->get() as $loan) {
                    $disbursed_loans = $disbursed_loans + 1;
                    $loan_due_items = \App\Helpers\GeneralHelper::loan_due_items($key->id);
                    $loan_paid_items = \App\Helpers\GeneralHelper::loan_paid_items($key->id);
                    $principal_disbursed = $principal_disbursed + $loan_due_items["principal"];
                    $interest_disbursed = $interest_disbursed + $loan_due_items["interest"];
                    $fees_disbursed = $fees_disbursed + $loan_due_items["fees"];
                    $penalty_disbursed = $penalty_disbursed + $loan_due_items["penalty"];
                    $principal_outstanding = $principal_outstanding + $loan_due_items["principal"] - $loan_paid_items["principal"];
                    $interest_outstanding = $interest_outstanding + $loan_due_items["interest"] - $loan_paid_items["interest"];
                    $fees_outstanding = $fees_outstanding + $loan_due_items["fees"] - $loan_paid_items["fees"];
                    $penalty_outstanding = $penalty_outstanding + $loan_due_items["penalty"] - $loan_paid_items["penalty"];
                }
                $disbursed = $principal_disbursed + $interest_disbursed + $fees_disbursed;
                $outstanding = $principal_outstanding + $interest_outstanding + $fees_outstanding + $penalty_outstanding;
                $total_disbursed = $total_disbursed + $disbursed;
                $total_disbursed_loans = $total_disbursed_loans + $disbursed_loans;
                $total_disbursed_principal = $total_disbursed_principal + $principal_disbursed;
                $total_disbursed_interest = $total_disbursed_interest + $interest_disbursed;
                $total_disbursed_fees = $total_disbursed_fees + $fees_disbursed;
                $total_disbursed_penalty = $total_disbursed_penalty + $penalty_disbursed;
                $total_outstanding_principal = $total_outstanding_principal + $principal_outstanding;
                $total_outstanding_interest = $total_outstanding_interest + $interest_outstanding;
                $total_outstanding_fees = $total_outstanding_fees + $fees_outstanding;
                $total_outstanding_penalty = $total_outstanding_penalty + $penalty_outstanding;
                $total_outstanding = $total_outstanding + $principal_outstanding + $interest_outstanding + $fees_outstanding + $penalty_outstanding;

                array_push($data, [
                    $key->name,
                    $disbursed_loans,
                    number_format($principal_disbursed, 2),
                    number_format($interest_disbursed, 2),
                    number_format($fees_disbursed, 2),
                    number_format($disbursed, 2),
                    number_format($principal_outstanding, 2),
                    number_format($interest_outstanding, 2),
                    number_format($fees_outstanding, 2),
                    number_format($penalty_outstanding, 2),
                    number_format($outstanding, 2),
                ]);
            }
            array_push($data, [
                "",
                $total_disbursed_loans,
                number_format($total_disbursed_principal, 2),
                number_format($total_disbursed_interest, 2),
                number_format($total_disbursed_fees, 2),
                number_format($total_disbursed, 2),
                number_format($total_outstanding_principal, 2),
                number_format($total_outstanding_interest, 2),
                number_format($total_outstanding_fees, 2),
                number_format($total_outstanding_penalty, 2),
                number_format($total_outstanding, 2),
            ]);

            Excel::create(trans_choice('general.product', 2) . ' ' . trans_choice('general.summary',
                    1),
                function ($excel) use ($data) {
                    $excel->sheet('Sheet', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', false, false);
                        $sheet->mergeCells('A1:K1');
                    });

                })->download('csv');
        }


    }

    public function general_report(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (isset($request->end_date)) {
            $date = $request->end_date;
        } else {
            $date = date("Y-m-d");
        }


        // Loan Product Pie Data
        $loan_product_data = [];
        $max_loan_product = null;
        $second_max_loan_product = null;
        $min_loan_product = null;
        $max_count = 0;
        $second_max_count = 0;
        $min_count = PHP_INT_MAX; // Initialize to a large value to ensure any count is smaller
        $total_customers = 0;

// Loop through each loan product to gather data
        foreach (LoanProduct::all() as $key) {
            // Count the number of loans based on the specified conditions
            if (empty($start_date)) {
                $count = Loan::where('loan_product_id', $key->id)
                    ->where('branch_id', session('branch_id'))
                    ->whereIn('status', ['disbursed', 'closed', 'written_off', 'rescheduled'])
                    ->count();
            } else {
                $count = Loan::where('loan_product_id', $key->id)
                    ->where('branch_id', session('branch_id'))
                    ->whereIn('status', ['disbursed', 'closed', 'written_off', 'rescheduled'])
                    ->whereBetween('release_date', [$start_date, $end_date])
                    ->count();
            }

            // Increment total customers
            $total_customers += $count;

            // Store loan product data in an array
            array_push($loan_product_data, [
                'product' => $key->name,
                'value' => $count
            ]);

            // Check if the current loan product has a higher count
            if ($count > $max_count) {
                $second_max_loan_product = $max_loan_product;
                $second_max_count = $max_count;
                $max_count = $count;
                $max_loan_product = $key->name;
            } elseif ($count > $second_max_count) {
                $second_max_count = $count;
                $second_max_loan_product = $key->name;
            }

            // Check if the current loan product has a lower count
            if ($count < $min_count) {
                $min_count = $count;
                $min_loan_product = $key->name;
            }
        }

// Description below the graph with percentage
        $description4 = "Among the loan products, $max_loan_product stands out as the most availed, with $max_count loan(s), representing " . number_format(($max_count / $total_customers) * 100, 2) . "% of borrower(s). Following closely is $second_max_loan_product, representing the second-highest number of loans at $second_max_count, accounting for " . number_format(($second_max_count / $total_customers) * 100, 2) . "% of borrower(s). On the other hand, $min_loan_product has the lowest loan uptake, with $min_count loan(s), constituting " . number_format(($min_count / $total_customers) * 100, 2) . "% of borrower(s). Understanding these patterns can provide insights into the popularity and utilization of different loan products.";

// Now you can use the $description4 variable wherever you want to display this information.




        $monthly_net_income_data = array();
        $loop_date = date_format(date_sub(date_create($date),
            date_interval_create_from_date_string('1 years')),
            'Y-m-d');
        for ($i = 1; $i < 14; $i++) {
            $d = explode('-', $loop_date);
            //get loans in that period
            $total_income = 0;
            foreach (ChartOfAccount::where('account_type', 'income')->get() as $key) {
                $cr = JournalEntry::where('account_id', $key->id)->where('year',
                    $d[0])->where('month',
                    $d[1])->where('branch_id',
                    session('branch_id'))->sum('credit');
                $dr = JournalEntry::where('account_id', $key->id)->where('year',
                    $d[0])->where('month',
                    $d[1])->where('branch_id',
                    session('branch_id'))->sum('debit');
                $balance = $cr - $dr;
                $total_income = $total_income + $balance;
            }
            $total_expenses = 0;
            foreach (ChartOfAccount::where('account_type', 'expense')->get() as $key) {
                $cr = JournalEntry::where('account_id', $key->id)->where('year',
                    $d[0])->where('month',
                    $d[1])->where('branch_id',
                    session('branch_id'))->sum('credit');
                $dr = JournalEntry::where('account_id', $key->id)->where('year',
                    $d[0])->where('month',
                    $d[1])->where('branch_id',
                    session('branch_id'))->sum('debit');
                $balance = $dr - $cr;
                $total_expenses = $total_expenses + $balance;
            }
            array_push($monthly_net_income_data, array(
                'month' => date_format(date_create($loop_date),
                    'M' . ' ' . $d[0]),
                'income' => $total_income,
                'expenses' => $total_expenses
            ));
            //add 1 month to start date
            $loop_date = date_format(date_add(date_create($loop_date),
                date_interval_create_from_date_string('1 months')),
                'Y-m-d');
        }
        //user registrations
//        $monthly_borrower_data = [];
//        $loop_date = date_format(date_sub(date_create($date),
//            date_interval_create_from_date_string('1 years')),
//            'Y-m-d');
//
//        for ($i = 1; $i < 14; $i++) {
//            $d = explode('-', $loop_date);
//            //get loans in that period
//            $count = Borrower::where('year',
//                $d[0])->where('month',
//                $d[1])->where('branch_id',
//                session('branch_id'))->count();
//            array_push($monthly_borrower_data, array(
//                'month' => date_format(date_create($loop_date),
//                    'M' . ' ' . $d[0]),
//                'value' => $count,
//            ));
//            //add 1 month to start date
//            $loop_date = date_format(date_add(date_create($loop_date),
//                date_interval_create_from_date_string('1 months')),
//                'Y-m-d');
//        }



        $monthly_borrower_data = [];
        $loop_date = date_format(date_sub(date_create($date),
            date_interval_create_from_date_string('1 years')),
            'Y-m-d');

        $previous_3_months_counts = [];

// Calculate borrower counts for the past 13 months
        for ($i = 1; $i < 13; $i++) {
            $d = explode('-', $loop_date);
            $count = Borrower::where('year', $d[0])
                ->where('month', $d[1])
                ->where('branch_id', session('branch_id'))
                ->count();

            array_push($monthly_borrower_data, [
                'month' => date_format(date_create($loop_date), 'M Y'),
                'value' => $count,
            ]);

            // Keep track of counts for the past 3 months
            if ($i > 9 && $i <= 12) {
                $previous_3_months_counts[] = $count;
            }

            $loop_date = date_format(date_add(date_create($loop_date), date_interval_create_from_date_string('1 months')), 'Y-m-d');
        }

        $current_month = date('Y-m');
        $current_month_count = Borrower::where('year', date('Y'))
            ->where('month', date('m'))
            ->where('branch_id', session('branch_id'))
            ->count();

        array_push($monthly_borrower_data, [
            'month' => date_format(date_create($current_month), 'M Y'),
            'value' => $current_month_count,
        ]);

// Calculate average count for the previous 3 months
        $average_previous_3_months = count($previous_3_months_counts) > 0 ? array_sum($previous_3_months_counts) / count($previous_3_months_counts) : 0;

// Generating description
        $current_month_name = date_format(date_create($current_month), 'M Y');
        $description = "The following data visualization illustrates the monthly trend of borrower registrations over the past 13 months, showcasing the dynamic patterns in user engagement. In the current month of $current_month_name, $current_month_count individuals have registered as borrowers, reflecting the ongoing growth of your community. This figure is thoughtfully compared to the average of $average_previous_3_months borrowers in the preceding 3 months, providing valuable insights into the evolving trends and user acquisition dynamics. This analysis not only highlights the present vibrancy in borrower registrations but also aids in understanding the broader context of your platform's user engagement over time.";

// Now $description contains the text describing the graph with the corrected comparison and without double quotes






        $monthly_repayments_data = [];
        $loop_date = date_format(date_sub(date_create($date),
            date_interval_create_from_date_string('1 years')),
            'Y-m-d');
        for ($i = 1; $i < 14; $i++) {
            $d = explode('-', $loop_date);
            //get loans in that period
            $amount = LoanTransaction::where('transaction_type',
                'repayment')->where('reversed', 0)->where('year',
                $d[0])->where('month',
                $d[1])->where('branch_id',
                session('branch_id'))->sum('credit');
            array_push($monthly_repayments_data, array(
                'month' => date_format(date_create($loop_date),
                    'M' . ' ' . $d[0]),
                'value' => $amount,
            ));
            //add 1 month to start date
            $loop_date = date_format(date_add(date_create($loop_date),
                date_interval_create_from_date_string('1 months')),
                'Y-m-d');
        }




        $monthly_actual_expected_data = [];
        $monthly_disbursed_loans_data = [];
        $loop_date = date_format(date_sub(date_create($date),
            date_interval_create_from_date_string('1 years')),
            'Y-m-d');
        $current_month = date('Y-m');
        $payments_received_current_month = 0;
        $expected_payments_current_month = 0;

        for ($i = 1; $i < 14; $i++) {
            $d = explode('-', $loop_date);
            $actual = 0;
            $expected = 0;
            $principal = 0;

            // Calculate actual payments received
            $actual = $actual + LoanTransaction::where('transaction_type', 'repayment')
                    ->where('reversed', 0)
                    ->where('year', $d[0])
                    ->where('month', $d[1])
                    ->where('branch_id', session('branch_id'))
                    ->sum('credit');

            // Calculate expected payments
            foreach (Loan::select("loan_schedules.principal", "loan_schedules.interest", "loan_schedules.penalty",
                "loan_schedules.fees")->where('loans.branch_id', session('branch_id'))
                         ->whereIn('loans.status', ['disbursed', 'closed', 'written_off'])
                         ->join('loan_schedules', 'loans.id', '=', 'loan_schedules.loan_id')
                         ->where('loan_schedules.deleted_at', NULL)
                         ->where('loan_schedules.year', $d[0])
                         ->where('loan_schedules.month', $d[1])
                         ->get() as $key) {
                $expected = $expected + $key->interest + $key->penalty + $key->fees + $key->principal;
                $principal = $principal + $key->principal;
            }

            array_push($monthly_actual_expected_data, [
                'month' => date_format(date_create($loop_date), 'M' . ' ' . $d[0]),
                'actual' => $actual,
                'expected' => $expected,
            ]);

            array_push($monthly_disbursed_loans_data, [
                'month' => date_format(date_create($loop_date), 'M' . ' ' . $d[0]),
                'value' => $principal,
            ]);

            // Check if the current iteration is for the current month
            if ($d[0] == date('Y') && $d[1] == date('m')) {
                $payments_received_current_month = $actual;
                $expected_payments_current_month = $expected;
            }

            // add 1 month to start date
            $loop_date = date_format(date_add(date_create($loop_date),
                date_interval_create_from_date_string('1 months')),
                'Y-m-d');
        }

// Format numbers with commas
        $payments_received_current_month_formatted = number_format($payments_received_current_month);
        $expected_payments_current_month_formatted = number_format($expected_payments_current_month);
        // Calculate remaining payments
        $remaining_payments_current_month = max(0, $expected_payments_current_month - $payments_received_current_month);

// Format numbers with commas
        $remaining_payments_current_month_formatted = number_format($remaining_payments_current_month);

// Now you can add the updated description below the graph without double quotes





// Now you can add the description below the graph
        $description1 = "The graph above illustrates a detailed comparison between Payments Received and Expected Payments over time. In the current month of " . date_format(date_create($current_month), 'M Y') . ", there have been $payments_received_current_month_formatted received, while the expected payments amount to $expected_payments_current_month_formatted. This analysis reveals that there are $remaining_payments_current_month_formatted in remaining payments for this period, highlighting the financial dynamics and progress in meeting the anticipated payments.";

//        $description1 = "$payments_received_current_month_formatted in " . date_format(date_create($current_month), 'M Y');
//        $description2 = "$expected_payments_current_month_formatted in " . date_format(date_create($current_month), 'M Y');
//        $description3 = "$remaining_payments_current_month_formatted in " . date_format(date_create($current_month), 'M Y');






        $loan_product_data = json_encode($loan_product_data);
        $description4 = json_encode($description4);
        $monthly_net_income_data = json_encode($monthly_net_income_data);
        $monthly_borrower_data = json_encode($monthly_borrower_data);
        $description = json_encode($description);
        $monthly_repayments_data = json_encode($monthly_repayments_data);
        $monthly_actual_expected_data = json_encode($monthly_actual_expected_data);
        $description1 = json_encode($description1);

        $monthly_disbursed_loans_data = json_encode($monthly_disbursed_loans_data);
        return view('company_report.general_report',
            compact('loan_product_data', 'description4', 'monthly_net_income_data', 'monthly_borrower_data', 'description', 'monthly_repayments_data',
                'monthly_actual_expected_data', 'description1', 'monthly_disbursed_loans_data','start_date','end_date'));
    }

    public function journal(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        return view('financial_report.journal',
            compact('start_date',
                'end_date'));
    }

    public function ledger(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        return view('financial_report.ledger',
            compact('start_date',
                'end_date'));
    }

    public function savings_transactions(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($start_date)) {
            $data = SavingTransaction::where('reversed', 0)->where('branch_id',
                session('branch_id'))->whereBetween('date',
                [$start_date, $end_date])->get();
        }
        return view('savings_report.savings_transactions',
            compact('start_date',
                'end_date', 'data'));
    }

    public function savings_transactions_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($start_date)) {
            $data =  SavingTransaction::where('reversed', 0)->where('branch_id',
                session('branch_id'))->whereBetween('date',
                [$start_date, $end_date])->get();
            $pdf = PDF::loadView('savings_report.savings_transactions_pdf', compact('start_date',
                'end_date', 'data'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download(trans_choice('general.saving', 2) . ' ' . trans_choice('general.transaction',
                    2) . ".pdf");
        }


    }

    public function savings_transactions_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($start_date)) {
            $data = [];
            array_push($data, [
                trans_choice('general.saving', 2) . ' ' . trans_choice('general.transaction',
                    2)
            ]);
            array_push($data, [
                trans_choice('general.borrower', 1),
                trans_choice('general.account', 1),
                trans_choice('general.type', 1),
                trans_choice('general.debit', 1),
                trans_choice('general.credit', 1),
                trans_choice('general.date', 1),
                trans_choice('general.receipt', 1),
                trans_choice('general.payment', 1) . " " . trans_choice('general.method', 1),
            ]);
            $total_deposited = 0;
            $total_withdrawn = 0;
            $cr = 0;
            $dr = 0;
            foreach (SavingTransaction::where('reversed', 0)->where('branch_id',
                session('branch_id'))->whereBetween('date',
                [$start_date, $end_date])->get() as $key) {
                $dr = $dr + $key->debit;
                $cr = $cr + $key->credit;
                if (!empty($key->borrower)) {
                    $borrower = $key->borrower->first_name . " " . $key->borrower->last_name;
                } else {
                    $borrower = "";
                }
                if (!empty($key->payment_method)) {
                        $payment_method = $key->payment_method->name;
                } else {
                    $payment_method = "";
                }
                if (!empty($key->savings)) {
                    if (!empty($key->savings->savings_product)) {
                        $savings_product = $key->savings->savings_product->name;
                    }else{
                        $savings_product = "";
                    }

                } else {
                    $savings_product = "";
                }
                array_push($data, [
                    $borrower,
                    $key->savings_id,
                    $savings_product,
                    number_format($key->debit, 2),
                    number_format($key->credit, 2),
                    $key->date,
                    $key->receipt,
                    $payment_method,
                ]);
            }
            array_push($data, [
                "",
                "",
                "",
                number_format($dr, 2),
                number_format($cr, 2),
                "",
                "",
                "",
            ]);

            Excel::create(trans_choice('general.saving', 2) . ' ' . trans_choice('general.transaction',
                    2),
                function ($excel) use ($data) {
                    $excel->sheet('Sheet', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', false, false);
                        //$sheet->mergeCells('A1:J1');
                    });

                })->download('xls');
        }


    }
    public function savings_transactions_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permiso Denegado");
            return redirect('/');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($start_date)) {
            $data = [];
            array_push($data, [
                trans_choice('general.saving', 2) . ' ' . trans_choice('general.transaction',
                    2)
            ]);
            array_push($data, [
                trans_choice('general.borrower', 1),
                trans_choice('general.account', 1),
                trans_choice('general.type', 1),
                trans_choice('general.debit', 1),
                trans_choice('general.credit', 1),
                trans_choice('general.date', 1),
                trans_choice('general.receipt', 1),
                trans_choice('general.payment', 1) . " " . trans_choice('general.method', 1),
            ]);
            $total_deposited = 0;
            $total_withdrawn = 0;
            $cr = 0;
            $dr = 0;
            foreach (SavingTransaction::where('reversed', 0)->where('branch_id',
                session('branch_id'))->whereBetween('date',
                [$start_date, $end_date])->get() as $key) {
                $dr = $dr + $key->debit;
                $cr = $cr + $key->credit;
                if (!empty($key->borrower)) {
                    $borrower = $key->borrower->first_name . " " . $key->borrower->last_name;
                } else {
                    $borrower = "";
                }
                if (!empty($key->payment_method)) {
                    $payment_method = $key->payment_method->name;
                } else {
                    $payment_method = "";
                }
                if (!empty($key->savings)) {
                    if (!empty($key->savings->savings_product)) {
                        $savings_product = $key->savings->savings_product->name;
                    }else{
                        $savings_product = "";
                    }

                } else {
                    $savings_product = "";
                }
                array_push($data, [
                    $borrower,
                    $key->savings_id,
                    $savings_product,
                    number_format($key->debit, 2),
                    number_format($key->credit, 2),
                    $key->date,
                    $key->receipt,
                    $payment_method,
                ]);
            }
            array_push($data, [
                "",
                "",
                "",
                number_format($dr, 2),
                number_format($cr, 2),
                "",
                "",
                "",
            ]);

            Excel::create(trans_choice('general.saving', 2) . ' ' . trans_choice('general.transaction',
                    2),
                function ($excel) use ($data) {
                    $excel->sheet('Sheet', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', false, false);
                        //$sheet->mergeCells('A1:J1');
                    });

                })->download('csv');
        }


    }
}
