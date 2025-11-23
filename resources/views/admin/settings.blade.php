<x-app-layout>
	<x-slot name="header">
		<div class="flex items-center justify-between">
			<div>
				<h2 class="font-semibold text-xl text-gray-800 leading-tight">Settings</h2>
				<p class="text-sm text-gray-500 mt-1">Configure application and payment settings</p>
			</div>
			<div><a href="{{ route('admin.dashboard') }}" class="px-3 py-2 text-sm bg-white rounded shadow">Back to Dashboard</a></div>
		</div>
	</x-slot>

	<div class="py-6">
		<div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white rounded-lg shadow p-4">
				<div class="flex gap-2 border-b pb-3 mb-4">
					<button class="tab-btn px-3 py-2 rounded bg-indigo-50 text-indigo-700" data-tab="general">General</button>
					<button class="tab-btn px-3 py-2 rounded" data-tab="payments">Payments</button>
					<button class="tab-btn px-3 py-2 rounded" data-tab="notifications">Notifications</button>
				</div>

				<form id="settings-form" class="space-y-4">
					<div class="tab-panel" data-panel="general">
						<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
							<div>
								<label class="block text-sm text-gray-600">Site name</label>
								<input name="site_name" class="w-full px-3 py-2 border rounded" value="{{ config('app.name') }}" />
							</div>
							<div>
								<label class="block text-sm text-gray-600">Admin email</label>
								<input name="admin_email" type="email" class="w-full px-3 py-2 border rounded" value="{{ Auth::user()->email ?? '' }}" />
							</div>
						</div>
					</div>

					<div class="tab-panel hidden" data-panel="payments">
						<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
							<div>
								<label class="block text-sm text-gray-600">Payment provider</label>
								<select name="payment_provider" class="w-full px-3 py-2 border rounded">
									<option value="stripe">Stripe</option>
									<option value="flutterwave">Flutterwave</option>
									<option value="paypal">PayPal</option>
								</select>
							</div>
							<div>
								<label class="block text-sm text-gray-600">Test mode</label>
								<select name="payment_mode" class="w-full px-3 py-2 border rounded">
									<option value="test">Test</option>
									<option value="live">Live</option>
								</select>
							</div>
						</div>
					</div>

					<div class="tab-panel hidden" data-panel="notifications">
						<div class="space-y-3">
							<label class="flex items-center gap-3">
								<input type="checkbox" name="email_notifications" checked />
								<span class="text-sm text-gray-600">Email notifications</span>
							</label>
							<label class="flex items-center gap-3">
								<input type="checkbox" name="sms_notifications" />
								<span class="text-sm text-gray-600">SMS notifications</span>
							</label>
						</div>
					</div>

					<div class="pt-4 flex justify-end">
						<button type="button" id="save-settings" class="px-4 py-2 bg-indigo-600 text-white rounded">Save settings</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<script>
		// Tabs
		document.querySelectorAll('.tab-btn').forEach(btn => {
			btn.addEventListener('click', () => {
				document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('bg-indigo-50','text-indigo-700'));
				btn.classList.add('bg-indigo-50','text-indigo-700');
				const tab = btn.getAttribute('data-tab');
				document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
				document.querySelector(`.tab-panel[data-panel="${tab}"]`).classList.remove('hidden');
			});
		});

		// Save (placeholder)
		document.getElementById('save-settings')?.addEventListener('click', () => {
			alert('Settings saved (UI-only). Integrate with backend to persist.');
		});
	</script>
</x-app-layout>
