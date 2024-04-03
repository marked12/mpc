@extends('client.layout')
@section('title')
     {{ trans_choice('general.guide',1) }}
@endsection

@section('content')

    <div class="row">

        <div class="col-md-12">

            <div class="panel panel-white">
                <div class="panel-heading">
                    <h6 class="panel-title">{{ trans_choice('general.guide',1) }}</h6>
                </div>

                <div class="panel-body table-responsive ">
                    <h6><b>Emergency Loan</b></h6>
                        <ol>
                            <li>Emergency loan shall not be less than Eight Thousand Pesos (P8, 000.00) and not more than Ten Thousand Pesos (P 10,000.00) with a maturity of twelve (12) mornths.</li>
                            <li>The borrower shall submit, along with the other duly accomplished application forms, his/her salary pay slips for the past 3 months duly certified by the institution accountant with a net pay of not less than:
                                <br>1. P7,011 for a P 8,000.00 loan application</br>
                                2. P 7,188 for a P 10,000.00 loan application</li>
                            <li>Emergency loan shall earn a 1% diminishing interest per month and a service fee of 4%. </li>
                            <li>Monthly amortization shall be determined using the diminishing scheme for 12 months and shall be deducted through a salary deduction. Cash payments are only allowed if
                                the borrower will pay in full amount his/her remaining total balance.</li>
                            <li>The deduction of the amortization shall take effect on the month after the loan approval.</li>
                            <li>Failure to pay shall gain an interest of 1% on top of the unpaid computed amortization and shall be deducted a month after or the next payroll period through a salary deduction.</li>
                            <li> Borrower may renew his/her educational loan provided that he/she has paid of at least 6 months of his/her previous loan. Remaining balance will be deducted from the new loan amount.
                                To renew loan applications, a statement of account issued by the bookkeeper is also required.</li>
                            <li>Application for Emergency loan shall pass through the scrutiny of the credit committee and shall be endorsed to the BOD Chair for approval.</li>
                        </ol>
                    <br>
                    <h6><b>Educational Loan</b></h6>
                        <ol>
                            <li>Educational loan shall be either twenty Thousand Pesos (P 20,000.00) or Thirty Thousand Pesos (P 30,000.00) only with a 12 months maturity period.</li>
                            <li>The borrower shall submit, along with the other duly accomplished application forms, his/her salary payslips for the recent 3 months which are all duly certified by the institution accountant with a net take home pay of not less than:
                                <br>a) P 8,076 for a P 20,000.00 loan application
                                <br>b) P8,963 for an P 30,000.00 loan application
                                <br>A photocopy of a payroll authenticated by the accountant may also be considered in lieu of the payslip. A co-maker must have at least P20,000 Capital Build Up.</li>
                            <li>Educational loan shall earn a 1% diminishing interest per month and a service fee of 4%. </li>
                            <li>Monthly amortization shall be determined using the diminishing scheme for 12 months and shall be deducted through a salary deduction. Cash payments are only allowed if the borrower will pay in full amount his/her remaining balance.</li>
                            <li>The deduction of the amortization shall take effect on the month after the loan approval.</li>
                            <li>Failure to pay shall gain an interest of 1% on top of the unpaid computed amortization and shall be deducted a month after or the next payroll period through a salary deduction.</li>
                            <li>Borrower may renew his/her educational loan provided that he/she has paid of at least 6 months of his/her previous loan. Remaining balance will be deducted from the new loan amount.
                                    To renew loan application, a statement of account issued by the bookkeeper is required. </li>
                            <li>Application for Educational loan shall pass through the scrutiny of the credit committee and shall be endorsed to the BOD Chair for approval.</li>
                        </ol>
<br>
                    <h6><b>Bonus Loan</b></h6>
                        <ol>
                            <li>Bonus Loan shall mean Mid-year and year-end bonus.</li>
                            <li>Bonus Loan shall be available to members and non-members of DOSCST MPC.</li>
                            <li>Borrowers can avail as much as 100% equivalent of their mid-year or year end bonus.</li>
                            <li>The borrower shall submit, along with the duly accomplished application form his or her latest pay slip duly certified by the accountant and the borrower. A certification stating that the borrower has not availed any bonus loan other than DOSCST-MPC is also required. TwO co-borrowers are necessary as well.</li>
                            <li>Bonus loan shall earn a 1% interest per month and a service fee of 4% which will be deducted prior to the release.</li>
                            <li>Collection of the bonus loan shall be collected as one through the institution and not per borrower.</li>
                            <li>Failure to pay shall gain an interest of 1% per month of the unpaid amount without prior notice.</li>
                            <li>Renewal of the same loan window shall be possible after the receipt of the previous bonus loan payment.</li>
                            <li>Application for Bonus Loan shall pass through the scrutiny of the credit committee and shall be endorsed to the BOD Chair for approval.</li>

                        </ol>
                    <br>
                    <h6><b>Regular Loan</b></h6>
                        <ol>
                            <li>Regular loan shall be sixty thousand (60K) or eighty thousand (80K) or one hundred thousand (100K) Pesos with 24 months maturity period or Two hundred thousand (200K) pesos with 36 months maturity.</li>
                            <li>To qualify, member borrower must be a borrower with <i>plantılla</i> position with at least P30,000 Capital Build Up (CBU).</li>
                            <li>The borrower shall comply and submit the following requirements
                                <br>a) Accomplished application form.
                                <br>b) Accomplished Insurance form
                                <br>c) Co-makers must have at least P30,000.00 Capital Build Up and in <i>plantılla</i> employment status.
                                <br>d) Photocopy of 3 previous month's payslips with a net take home pay of not less than P9,113 (or 60K), 10,0S1 (for 8Ok), 10,988 (for 100k), and 12,884 (for 200k) and must be duly signed by the borrower and the accountant. A photocopy of a pay roll authenticated by the accountant may also be considered in lieu of the payslıp.</li>
                            <li>Regular loan shall earn a 1% diminishing interest per month anda service fee of 4° %</li>
                            <li>Monthly amortization shall be determined using the diminishing scheme for 24 or 36 months and shall be deducted through a salary deduction. Cash payments are only allowed if the borrower will pay in full amount his/ her remaining total balance.</li>
                            <li>The deduction of the amortization shall take effect on the month after the loan approval.</li>
                            <li>Failure to pay shall gain an interest of 1°o on top of the unpaid computed amortization and
                                shall be deducted a month after or the next payroll period through a salary deduction</li>
                            <li>Borrower may renew his/her regular loan provided that he/she has paid of at least 12 months of his her previous loan. Remaining balance will be deducted from the new loan amount</li>
                            <li>Application for Regular L.oan shall pass through the scrutiny of the credit committee and shall be endorsed to the BOD Chair for approval.</li>
                        </ol>


                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {

        });
    </script>
@endsection
