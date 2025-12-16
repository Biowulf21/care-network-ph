<div class="p-6 max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Doctors</h1>
        <a href="{{ route('doctors.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded">Add Doctor</a>
    </div>

    <div class="bg-white rounded shadow p-4">
        <table class="w-full text-left">
            <thead>
                <tr class="text-sm text-gray-500">
                    <th>Name</th>
                    <th>Clinic</th>
                    <th>Specialty</th>
                    <th>Contact</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($doctors as $doc)
                    <tr class="border-t">
                        <td class="py-3">{{ $doc->name }}</td>
                        <td>{{ $doc->clinic->name ?? 'N/A' }}</td>
                        <td>{{ $doc->specialty }}</td>
                        <td>{{ $doc->phone }} {{ $doc->email ? ' • ' . $doc->email : '' }}</td>
                        <td class="text-right"><a href="{{ route('doctors.edit', $doc) }}" class="text-blue-600">Edit</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
