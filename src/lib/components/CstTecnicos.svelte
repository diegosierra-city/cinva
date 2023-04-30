<script lang="ts">
	import type { Tecnico } from '$lib/types/Tecnico';
	import { onMount } from 'svelte/internal';
	import { cookie_info, apiKey, userNow } from '../../store';
	import { Moon } from 'svelte-loading-spinners';
	import Messages from '$lib/components/Messages.svelte';
	import type { Message } from '$lib/types/Message';
	import CstNewPassword from './CstNewPassword.svelte';

	let m_show: boolean = false;
	let message: Message;

	let listTecnicos: Array<Tecnico> = [];

	const urlAPI = $apiKey.urlAPI;
	const urlFiles = $apiKey.urlFiles;
	//// GET
	console.log(
		urlAPI +
			'?ref=load-list&user_id=' +
			$userNow.id +
			'&time_life=' +
			$userNow.user_time_life +
			'&token=' +
			$userNow.token +
			'&folder=cst_tecnicos&campo=tipo&campoV=tecnico&orden=nombre'
	);
	onMount(async () => {
		await fetch(
			urlAPI +
				'?ref=load-list&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=cst_tecnicos&campo=tipo&campoV=tecnico&orden=nombre'
		)
			.then((response) => response.json())
			.then((result) => {
				console.log('recibiendo:');

				listTecnicos = result;
				console.log(listTecnicos);
			})
			.catch((error) => console.log(error.message));
	});

	

	function addTecnico() {
		let newTecnico: Tecnico = {
			id: Date.now(),
			nombre: '',
			documento: '',
			imagen: '',
			email: '',
			tipo: 'tecnico',
			activo: true
		};

		listTecnicos = [...listTecnicos, newTecnico];
	}

	let fileinput: any;
	let image_id: number = 0;
	let image_position: number = 0;

	const upload = (e: any) => {
		//console.log('FF:'+image_id)
		if (image_id > 1000000) {
			message = {
				title: 'Error',
				text: 'Primero da click en el botón de Guardar',
				class: 'message-red',
				accion: ''
			};
			m_show = true;
		} else {
			let image = e.target.files[0];

			listTecnicos[image_position].imagen = 'load';
			const dataArray = new FormData();
			dataArray.append('user_id', String($userNow.id));
			dataArray.append('time_life', String($userNow.user_time_life));
			dataArray.append('token', $userNow.token);
			dataArray.append('id', String(image_id));
			dataArray.append('position', '');
			dataArray.append('uploadFile', image);
			dataArray.append('folder', 'cst_tecnicos');
			fetch(urlAPI + '?ref=upload', {
				method: 'POST',
				body: dataArray
			})
				.then((response) => response.json())
				.then((result) => {
					if (result[0]['error']) {
						message = {
							title: 'error',
							text: result[0]['error'],
							class: 'message-red',
							accion: ''
						};
						m_show = true;
					} else {
						listTecnicos[image_position] = result[0];
						message = {
							title: 'Publicar',
							text: 'Imagen Publicada',
							class: 'message-green',
							accion: ''
						};
						m_show = true;
					}
				})
				.catch((error) => console.log(error.message));
		}
	};

	const saveTecnicos = async () => {
		console.log(listTecnicos);
		await fetch(urlAPI + '?ref=save-list', {
			method: 'POST', //POST - PUT - DELETE
			body: JSON.stringify({
				user_id: $userNow.id,
				time_life: $userNow.user_time_life,
				token: $userNow.token,
				list: listTecnicos,
				folder: 'cst_tecnicos',
				campo: 'tipo',
				campoV: 'tecnico',
				orden: 'nombre'
				//password: pass,
			}),
			headers: {
				'Content-type': 'application/json; charset=UTF-8'
			}
		})
			.then((response) => response.json())
			//.then(result => console.log(result))
			.then((result) => {
				console.log('ok');
				console.log(result);
				message = {
					title: 'Guardar',
					text: 'Se guardó correctamente',
					class: 'message-green',
					accion: ''
				};
				m_show = true;

				//++listTecnicos = result;
			})

			.catch((error) => console.error(error));

		//  });
	};

	let folder='cst_tecnicos'

	
</script>

<svelte:head>
	<title>Tènicos</title>
</svelte:head>

<div class="p-3 w-full ">
	<div class="relative overflow-x-auto shadow-md sm:rounded-lg w-full">
		<div class="flex">
			<button class="btn-green mr-2 flex" on:click={saveTecnicos}>
    <i class="fa fa-save mt-1 mr-2" />
     Guardar</button>
			<button class="btn-primary flex" on:click={addTecnico}>
				<i class="fa fa-plus mt-1 mr-2" />
				Agregar Nuevo Técnico</button
			>
		</div>
		<table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
			<thead class="text-xs text-white uppercase bg-primary dark:bg-gray-700 dark:text-gray-400">
				<th scope="col" class="" />
				<th scope="col" class=""> Nombre</th>
				<th scope="col" class=""> Documento </th>
				<th scope="col" class=""> Email </th>
				<th scope="col" class=""> Nueva clave </th>
				<th scope="col" class=""> Foto </th>
				<th scope="col" class=""> Activo </th>
			</thead>
			<tbody>
				{#each listTecnicos as tecnico, i}
					<tr class="bg-white border-b hover:bg-aliceblue">
						<td class="font-bold">{i + 1}</td>
						<td>
							<input type="text" class="inputA" bind:value={tecnico.nombre} placeholder="nombre" />
						</td>
						<td>
							<input type="text" class="inputA" bind:value={tecnico.documento} placeholder="documento" />
						</td>

						<td>
							<input type="email" class="inputA" bind:value={tecnico.email} placeholder="email" />
						</td>
						<td class="">
							{#if tecnico.id < 1000000}
								<CstNewPassword bind:m_show bind:message bind:tecnico_id={tecnico.id} {folder}/>
							{/if}
						</td>

						<td>
							{#if tecnico.id < 1000000}
								<div class="flex justify-center">
									{#if tecnico.imagen == 'load'}
										<Moon size="20" unit="px" duration="2s" />
									{:else if tecnico.imagen != ''}
										<div class="flex items-center">
											<img
												class="w-16 h-auto mr-2"
												src="{urlFiles}imagenes/cst_tecnicos/M{tecnico.imagen}"
												alt={tecnico.nombre}
											/>

											<button
												class="btn-min bg-red mr-2 flex"
												on:click={() => {
													tecnico.imagen = '';
												}}
											>
												<i class="fa fa-trash-o m-1" />
											</button>
										</div>
									{/if}
									<div class="flex items-center">
										<button
											class="btn-min bg-primary"
											on:click={() => {
												image_id = tecnico.id;
												image_position = i;
												fileinput.click();
											}}
										>
											<i class="fa fa-camera m-1" />
										</button>
									</div>
								</div>
							{/if}
						</td>

						<td class="text-center"><input type="checkbox" bind:checked={tecnico.activo} /></td>
					</tr>
				{:else}
					Sin registros
				{/each}
			</tbody>
		</table>
	</div>
</div>

<input
	style="display:none"
	type="file"
	accept=".jpg, .jpeg, .png"
	on:change={(e) => {
		upload(e);
	}}
	bind:this={fileinput}
/>

{#if m_show == true}
	<Messages bind:m_show bind:message />
{/if}
