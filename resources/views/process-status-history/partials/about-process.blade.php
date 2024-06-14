<div class="about-process styled-box">
    <h1 class="main-title">{{ __('About process') }}</h1>

    <table class="table secondary-table">
        <tbody>
            <tr>
                <td>{{ __('ID') }}:</td>
                <td>{{ $process->id }}</td>
            </tr>

            <tr>
                <td>{{ __('Process status') }}:</td>
                <td>{{ $process->status->name }}</td>
            </tr>

            <tr>
                <td>{{ __('Analyst') }}:</td>
                <td>{{ $process->manufacturer->analyst->name }}</td>
            </tr>

            <tr>
                <td>{{ __('BDM') }}:</td>
                <td>{{ $process->manufacturer->bdm->name }}</td>
            </tr>

            <tr>
                <td>{{ __('Manufacturer') }}:</td>
                <td>{{ $process->manufacturer->name }}</td>
            </tr>

            <tr>
                <td>{{ __('Generic') }}:</td>
                <td>{{ $process->product->inn->name }}</td>
            </tr>

            <tr>
                <td>{{ __('Form') }}:</td>
                <td>{{ $process->product->form->name }}</td>
            </tr>

            <tr>
                <td>{{ __('Dosage') }}:</td>
                <td>{{ $process->product->dosage }}</td>
            </tr>

            <tr>
                <td>{{ __('Pack') }}:</td>
                <td>{{ $process->product->pack }}</td>
            </tr>
        </tbody>
    </table>
</div>
