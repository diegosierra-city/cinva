<script lang="ts">
	import { apiKey, cookie_info, cookie_update, userNow } from '../../store';
	import Messages from '$lib/components/Messages.svelte';
import {base} from '$app/paths'
	import type { Message } from '$lib/types/Message';

	let m_show: boolean = false;
	let message: Message;

	const urlAPI = $apiKey.urlAPI + '?ref=login'; //login
	let login = {
		email: '',
		pass: ''
	};

	const login_run = () => {
		//console.log("xx");
		//// POST
		//onMount( async () => {
		fetch(urlAPI, {
			method: 'POST', //POST - PUT - DELETE
			body: JSON.stringify({
				email: login.email,
				password: login.pass
			}),
			headers: {
				'Content-type': 'application/json; charset=UTF-8'
			}
		})
			.then((response) => response.json())
			//.then(result => console.log(result))
			.then((result) => {
				//console.log(result)

				if (result[0]['error']) {
					message = {
						title: 'error',
						text: result[0]['error'],
						class: 'message-red',
						accion: ''
					};
					m_show = true;
				} else {
					//console.log('ok')
					// console.log(result)
					$userNow = result[0][0];
					cookie_update('user', JSON.stringify(result[0][0]));
				}
			})
			.catch((error) => {
				message = {
					title: 'error',
					text: error.message,
					class: 'message-red',
					accion: ''
				};
				m_show = true;
			});

		//  });
	};
</script>

<svelte:head>
	<title>Cinva - login</title>
</svelte:head>

<div
	class="p-10 bg-primary rounded-lg w-10/12 sm:w-8/12 md:w-6/12 lg:w-4/12 mx-auto mt-12 border"
>
	<div
		class="col-span-6 bg-white shadow-lg rounded lg:px-10 sm:px-6 sm:py-10 px-2 py-6"
	>
	<img src="/LogoCinva80.png" class="mx-auto" alt="">
		<p class="text-2xl font-bold leading-6 text-primary mt-4">Ingreso</p>

		<form on:submit|preventDefault={login_run}>
			<div class="mt-4">
				<label for="email"> Email</label>
				<input
					autocomplete="email"
					required
					type="email"
					class="inputA" name="email"
					placeholder="ej: john@gmail.com "
					bind:value={login.email}
				/>
			</div>
			<div class="mt-6 w-full">
				<label for="pass"> Clave </label>
				
					<input
						type="password"
						class="inputA" name="pass"
						autocomplete="current-password" placeholder="Clave"
						required
						bind:value={login.pass}
					/>
				
			</div>
			<div class="mt-8">
				<button type="submit" class="btn-primary-full">Ingresar</button>
			</div>
		</form>
	</div>
	
</div>

{#if m_show == true}
	<Messages bind:m_show bind:message />
{/if}
