@extends('client.layout')
@section('title')
    {{ trans_choice('general.apply',1) }}
@endsection

@section('content')

    <div class="row">

        <div class="col-md-12">

            <div class="panel panel-white">
                <div class="panel-heading">
                    <h6 class="panel-title">{{ trans_choice('general.apply',1) }}</h6>

                    <div class="heading-elements">
                    </div>
                </div>
                {!! Form::open(array('url' => url('client/application/store'), 'method' => 'post', 'name' => 'form',"enctype"=>"multipart/form-data")) !!}
                <div class="panel-body">
                    <div class="form-group">
                        {!! Form::label('branch_id',trans_choice('general.branch',1),array('class'=>'')) !!}
                        {!! Form::select('branch_id',$branches,null, array('class' => 'form-control','placeholder'=>'','required'=>'required')) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('loan_product_id',trans_choice('general.product',1),array('class'=>'')) !!}
                        {!! Form::select('loan_product_id',$products,null, array('class' => 'form-control','placeholder'=>'','required'=>'required')) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('amount',trans_choice('general.amount',1),array('class'=>'')) !!}
                        {!! Form::text('amount',null, array('class' => 'form-control touchspin', 'placeholder'=>'','required'=>'required')) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('files',trans_choice('general.instruction',1),array('class'=>'')) !!}
                        <br>
                        <a href="/download/loan.pdf" target="_blank" class="btn btn-primary"> Loan Application Form</a>
                        {!! Form::label('',null, array('class' => 'form-control', ''=>'','required'=>'required')) !!}
                    </div><br>
                    <div class="form-group ">
                        {!! Form::label('files',trans_choice('general.application_form',1),array('class'=>'col-sm-3 control-label')) !!}
                        <div class="col-sm-5">
                            {!! Form::file('files[]', array('class' => 'form-control file-styled', 'multiple'=>"multiple")) !!}
                        </div>
{{--                        <div class="col-sm-4">--}}
{{--                        </div>--}}
                    </div>
                    <br><br>
                    <div class="form-group">
                        {!! Form::label('files',trans_choice('general.salary_pay_slips',1),array('class'=>'col-sm-3 control-label')) !!}
                        <div class="col-sm-5">
                            {!! Form::file('files[]', array('class' => 'form-control file-styled', 'multiple'=>"multiple")) !!}
                        </div>
{{--                        <div class="col-sm-4">--}}
{{--                        </div>--}}
                    </div>
                    <br><br>
                    <div class="form-group">
                        {!! Form::label('files',trans_choice('general.certification_for_bonus_loan',1),array('class'=>'col-sm-3 control-label')) !!}
                        <div class="col-sm-5">
                            {!! Form::file('files[]', array('class' => 'form-control file-styled', 'multiple'=>"multiple")) !!}
                        </div>
{{--                        <div class="col-sm-4">--}}
{{--                        </div>--}}
                    </div>
                    <br><br>
                    <div class="form-group">
                        {!! Form::label('files',trans_choice('general.insurance_form_regular_loan',1),array('class'=>'col-sm-3 control-label')) !!}
                        <div class="col-sm-5">
                            {!! Form::file('files[]', array('class' => 'form-control file-styled', 'multiple'=>"multiple")) !!}
                        </div>
{{--                        <div class="col-sm-4">--}}
{{--                        </div>--}}
                    </div>
                    <br><br>
                    <div class="form-group">
                        {!! Form::label('files',trans_choice('general.authenticated_pay_roll',1),array('class'=>'col-sm-3 control-label')) !!}
                        <div class="col-sm-5">
                            {!! Form::file('files[]', array('class' => 'form-control file-styled', 'multiple'=>"multiple")) !!}
                        </div>
{{--                        <div class="col-sm-4">--}}
{{--                        </div>--}}
                    </div>
                    <br><br>
                    <div class="form-group">
                        {!! Form::label('notes',trans_choice('general.note',2),array('class'=>'')) !!}
                        {!! Form::textarea('notes',null, array('class' => 'form-control', 'rows'=>"3")) !!}
                    </div>
                </div>
                <div class="panel-footer">
                    <button type="submit" class="btn btn-primary pull-right">{{trans_choice('general.save',1)}}</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <script>
        // $(document).ready(function () {
        //     // Validate loan amount on form submission
        //     $('form').submit(function (e) {
        //         var loanProductId = $('select[name="loan_product_id"]').val();
        //         var amount = parseFloat($('input[name="amount"]').val()) || 0;
        //
        //         console.log('Loan Product ID:', loanProductId);
        //         console.log('Amount:', amount);
        //
        //         // Check if the value is NaN
        //         if (isNaN(amount)) {
        //             alert('Please enter a valid numeric value for the loan amount.');
        //             e.preventDefault(); // Prevent form submission
        //             return;
        //         }
        //
        //         // Set loan amount range based on the selected loan product
        //         switch (parseInt(loanProductId)) {
        //             case 6: // PEI Loan
        //                 if (amount < 1000 || amount > 5000) {
        //                     alert('PEI Loan amount must be between 1000 and 5000');
        //                     e.preventDefault(); // Prevent form submission
        //                 }
        //                 break;
        //
        //             case 1: // Emergency Loan
        //                 if (amount < 8000 || amount > 10000) {
        //                     alert('Emergency Loan amount must be between 8000 and 10000');
        //                     e.preventDefault(); // Prevent form submission
        //                 }
        //                 break;
        //
        //             case 2: // Educational Loan
        //                 if (amount < 20000 || amount > 30000) {
        //                     alert('Educational Loan amount must be between 20000 and 30000');
        //                     e.preventDefault(); // Prevent form submission
        //                 }
        //                 break;
        //
        //             case 3: // Gadget Loan
        //                 if (amount < 40000 || amount > 50000) {
        //                     alert('Gadget Loan amount must be between 40000 and 50000');
        //                     e.preventDefault(); // Prevent form submission
        //                 }
        //                 break;
        //
        //             case 4: // Regular Loan
        //                 if (amount < 60000 || amount > 200000) {
        //                     alert('Regular Loan amount must be between 60000 and 200000');
        //                     e.preventDefault(); // Prevent form submission
        //                 }
        //                 break;
        //
        //             case 5: // Bonus Loan
        //                 if (amount < 1000 || amount > 100000) {
        //                     alert('Bonus Loan amount must be between 1000 and 100000');
        //                     e.preventDefault(); // Prevent form submission
        //                 }
        //                 break;
        //
        //             default:
        //                 alert('Invalid loan product selected.');
        //                 e.preventDefault(); // Prevent form submission
        //                 break;
        //         }
        //     });
        // });

    </script>
@endsection
