<script lang="ts">
	import { cookie_info, apiKey, userNow } from '../../store';

	import type { Message } from '$lib/types/Message';

	export let m_show: boolean = false;
	export let message: Message;
	export let userEdit_id: number;
	export let folder: string;

	const urlAPI = $apiKey.urlAPI;
	let newpassword: string;

	const updateClave = async () => {
		await fetch(urlAPI + '?ref=updatePassword', {
			method: 'POST', //POST - PUT - DELETE
			body: JSON.stringify({
				user_id: $userNow.id,
				time_life: $userNow.user_time_life,
				token: $userNow.token,
				pass: newpassword,
				folder: folder,
				id: userEdit_id
			}),
			headers: {
				'Content-type': 'application/json; charset=UTF-8'
			}
		})
			.then((response) => response.json())
			//.then(result => console.log(result))
			.then((result) => {
				message = {
					title: 'Guardar',
					text: 'Se actualizó la Clave',
					class: 'message-green',
					accion: ''
				};
				m_show = true;
				newpassword = '';
				console.log(result);
			})

			.catch((error) => console.log(error.message));
	};
</script>

<div class="flex">
	<input type="password" class="inputA w-32" bind:value={newpassword} />
	<button class="text-xl" on:click={() => updateClave()}><i class="fa fa-chevron-circle-right text-green" /> </button>
</div>
