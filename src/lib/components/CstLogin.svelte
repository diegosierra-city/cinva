<script lang="ts">
 	import { apiKey, cookie_info, cookie_update, userNow } from '../../store';
	import Messages from '$lib/components/Messages.svelte';

	import type { Message } from '$lib/types/Message';

	let m_show: boolean = false;
	let message: Message;

 const urlAPI = $apiKey.urlAPI + '?ref=login';//login
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

    if(result[0]['error']){
     message = {
								title: 'error',
								text: result[0]['error'],
								class: 'message-red',
								accion: ''
							};
							m_show = true;
    }else{
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
    
   }
    );

		//  });
	};
</script>

<svelte:head>
	<title>CST - Ingreso</title>
</svelte:head>

<div
	class="p-10 bg-primary rounded-lg w-10/12 lg:w-8/12 xl:w-6/12 mx-auto grid grid-cols-6 gap-4  my-12"
>
	<div
		class="col-span-6 md:col-span-3 bg-white shadow-lg rounded lg:px-10 sm:px-6 sm:py-10 px-2 py-6"
	>
		
		<p class="text-2xl font-bold leading-6 text-primary mt-4">Ingreso al Sistema</p>

		<form on:submit|preventDefault={login_run}>
			<div class="mt-4">
				<label for="email" class="text-sm font-medium leading-none text-gray-800"> Email</label>
				<input
					autocomplete="email"
					required
					type="email"
					class="inputA"
					placeholder="ejemplo: john@gmail.com "
					bind:value={login.email}
				/>
			</div>
			<div class="mt-6 w-full">
				<label for="myInput" class="text-sm font-medium leading-none text-gray-800"> Clave </label>
				<div class="relative flex items-center justify-center">
					<input
						type="password"
						class="inputA"
						autocomplete="current-password"
						required
						bind:value={login.pass}
					/>
				</div>
			</div>
			<div class="mt-8">
				<button type="submit" class="btn-primary-full">Ingresar</button>
			</div>
		</form>
	</div>
	<div class="col-span-6 md:col-span-3 md:mt-0 mt-6 text-white ">
		<h2 class="">CST</h2>
		<div class="flex items-start">
			<p class="pl-2.5">Completo Control de Servicio Técnico</p>
		</div>
	</div>
</div>

{#if m_show == true}
	<Messages bind:m_show bind:message />
{/if}