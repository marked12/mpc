@extends('layouts.auth')
@section('title')
    {{trans_choice('general.app_name',1)}} | {{trans_choice('general.login',1)}}
@endsection

@section('content')
<div class="container" style="background-image: url({{ asset('assets/dist/img/logo.png') }}); width: 100%; height: 100%; background-size: cover; -moz-background-size: cover; -webkit-background-size: cover; -o-background-size: cover;">
    <div class="panel panel-body login-form">
        <!-- Role Selection -->
        <div class="text-center">
            <label for="role">Select Role:</label>
            <select id="role" name="role">
                <option value="client">Client</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <!-- Client Login Form -->
        <div class="client-form">
            {!! Form::open(array('url' => url('client-login'), 'method' => 'post', 'name' => 'client-form', 'class' => 'f-login-form')) !!}
            <div class="form-group has-feedback has-feedback-left">
                {!! Form::text('client_username', null, array('class' => 'form-control', 'placeholder' => trans_choice('general.username', 1), 'required' => 'required')) !!}
                <div class="form-control-feedback">
                    <i class="icon-user text-muted"></i>
                </div>
            </div>

            <div class="form-group has-feedback has-feedback-left">
                {!! Form::password('client_password', array('class' => 'form-control', 'placeholder' => trans('general.password'), 'required' => 'required')) !!}
                <div class="form-control-feedback">
                    <i class="icon-lock2 text-muted"></i>
                </div>
            </div>

            <div class="form-group login-options">
                <div class="row">
                    <div class="col-sm-6">
                        <label class="checkbox-inline">
                            <input type="checkbox" name="client_remember" class="styled">
                            {{ trans('general.remember') }}
                        </label>
                    </div>

                    <div class="col-sm-6 text-right">
                        <a href="javascript:;" class="client-forget-password">{{ trans('general.forgot_password') }}</a>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <center>
                    <button style="background-color: #4CAF50;" type="submit" class="btn btn-primary btn-block">{{ trans('general.login') }}</button>
                </center>
            </div>
            {!! Form::close() !!}
        </div>

        <!-- Admin Login Form -->
        <div class="admin-form">
            {!! Form::open(array('url' => url('admin-login'), 'method' => 'post', 'name' => 'admin-form', 'class' => 'f-login-form')) !!}
            <div class="form-group has-feedback has-feedback-left">
                {!! Form::text('admin_username', null, array('class' => 'form-control', 'placeholder' => trans_choice('general.username', 1), 'required' => 'required')) !!}
                <div class="form-control-feedback">
                    <i class="icon-user text-muted"></i>
                </div>
            </div>

            <div class="form-group has-feedback has-feedback-left">
                {!! Form::password('admin_password', array('class' => 'form-control', 'placeholder' => trans('general.password'), 'required' => 'required')) !!}
                <div class="form-control-feedback">
                    <i class="icon-lock2 text-muted"></i>
                </div>
            </div>

            <div class="form-group login-options">
                <div class="row">
                    <div class="col-sm-6">
                        <label class="checkbox-inline">
                            <input type="checkbox" name="admin_remember" class="styled">
                            {{ trans('general.remember') }}
                        </label>
                    </div>

                    <div class="col-sm-6 text-right">
                        <a href="javascript:;" class="admin-forget-password">{{ trans('general.forgot_password') }}</a>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <center>
                    <button style="background-color: #4CAF50;" type="submit" class="btn btn-primary btn-block">{{ trans('general.login') }}</button>
                </center>
            </div>
            {!! Form::close() !!}
        </div>

        <!-- Additional scripts for role selection and hiding/showing forms -->
        <script>
            $(document).ready(function () {
                $('.client-form, .admin-form').hide();

                $('#role').change(function () {
                    var selectedRole = $(this).val();
                    if (selectedRole === 'client') {
                        $('.client-form').show();
                        $('.admin-form').hide();
                    } else if (selectedRole === 'admin') {
                        $('.client-form').hide();
                        $('.admin-form').show();
                    }
                });

                $('.client-forget-password').click(function () {
                    // Handle client forget password action
                });

                $('.admin-forget-password').click(function () {
                    // Handle admin forget password action
                });
            });
        </script>
    </div>
</div>
@endsection
