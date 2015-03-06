<div class="form-group">
	<label for="cmbRole">Role</label>
	{!! Form::select('roles[]', $roles, null, ['class' => 'form-control', 'placeholder' => 'Application Role', 'multiple']) !!}
</div>
@if ($is_superuser)
	<div class="checkbox">
		<label>
			{!! Form::checkbox('superuser', 1) !!} Superuser
		</label>
	</div>
@endif