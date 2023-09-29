<script lang="ts">
	import type { UserControl } from '$lib/types/User';
	import { onMount } from 'svelte/internal';
	import { cookie_info, apiKey, userNow } from '../../store';
	import { Moon } from 'svelte-loading-spinners';
	import Messages from '$lib/components/Messages.svelte';
	import type { Message } from '$lib/types/Message';
	
	import Fuse from 'fuse.js';
 import ControlNewPassword from '$lib/components/ControlNewPassword.svelte';

	let m_show: boolean = false;
	let message: Message;

	let listUsers: Array<UserControl> = [];

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
			'&folder=cinva_users&orden=activo DESC,nombre'
	); //&campo=tipo&campoV=tecnico

	const loadUsers = async () => {
		await fetch(
			urlAPI +
				'?ref=load-list&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=cinva_users&orden=activo DESC,nombre'
		)
			.then((response) => response.json())
			.then((result) => {
				console.log('recibiendo:');

				listUsers = result;
				console.log(listUsers);
			})
			.catch((error) => console.log(error.message));
	};

	let newUser: UserControl;
	newUser = {
		id: Date.now(),
		nombre: '',
		documento: '',
		email: '',
		tipo: '',
		permisos: '',
		activo: true
	};

	function addUser() {
		listUsers = [...listUsers, newUser];
	}

	const saveUsers = async () => {
		console.log(listUsers);
		await fetch(urlAPI + '?ref=save-list', {
			method: 'POST', //POST - PUT - DELETE
			body: JSON.stringify({
				user_id: $userNow.id,
				time_life: $userNow.user_time_life,
				token: $userNow.token,
				list: listUsers,
				folder: 'cinva_users',
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
				console.log('GuardoListado', result);
				message = {
					title: 'Guardar',
					text: 'Se guardó correctamente',
					class: 'message-green',
					accion: ''
				};
				m_show = true;
				//////updateExcel();
				//++listUsers = result;
			})

			.catch((error) => console.log(error.message));

		//  });
	};

	let folder = 'cinva_users';
	let showUser: boolean = false;
	let userActual: UserControl = newUser;
	let positionEdit: number = -1;
	let showFicha: boolean = false;

	onMount(() => {
		loadUsers();
	});

	function actualizarView(p: UserControl) {
		listUsers[positionEdit] = p;
		updateExcel();
	}

	const updateExcel = async () => {
		await fetch(urlAPI + '?ref=excel-create', {
			method: 'POST', //POST - PUT - DELETE
			body: JSON.stringify({
				user_id: $userNow.id,
				time_life: $userNow.user_time_life,
				token: $userNow.token,
				list: listUsers,
				folder: 'cinva_users',
				orden: 'nombre,apellido',
				archivo: 'excelUsers.xlsx'
			}),
			headers: {
				'Content-type': 'application/json; charset=UTF-8'
			}
		})
			.then((response) => response.json())
			.then((result) => {
				console.log('excel', result);
			})
			.catch((error) => console.log(error.message));
	};

	//$: console.log('User:',userActual)
	let uploadExcel = false;
	let fileExcel: FileList;
	let file: any;

	function upload() {
		uploadExcel = true;
		const dataArray = new FormData();
		dataArray.append('user_id', String($userNow.id));
		dataArray.append('time_life', String($userNow.user_time_life));
		dataArray.append('token', $userNow.token);
		dataArray.append('file', 'excelUsers.xlsx');
		dataArray.append('folder', 'cinva_users');
		dataArray.append('uploadFile', fileExcel[0]);

		fetch(urlAPI + '?ref=upload-excel&prefix=', {
			method: 'POST',
			body: dataArray
		})
			.then((response) => response.json())
			.then((result) => {
				// Successfully uploaded
				console.log('upload:', result);
				message = {
					title: 'Subir Archivo',
					text: 'Se ha subido el archivo y se ha actualizado la base de datos',
					class: 'message-green',
					accion: ''
				};
				m_show = true;
				uploadExcel = false;
				loadUsers();
			})
			.catch((error) => console.log(error.message));
	}

	let filtroCiudades: any;
	let editar_ciudad: string = '';
	const options = {
		includeScore: true,
		useExtendedSearch: true,
		//keys: ['author', 'tags']
		keys: ['ciudad', 'codigo']
	};


</script>

<svelte:head>
	<title>Users</title>
</svelte:head>

<div class="p-3 w-full">
	<div class="relative pb-6 w-full">
		<div class="flex">
			<button class="btn-green mr-2 flex" on:click={saveUsers}>
				<i class="fa fa-save mt-1 mr-2" />
				Guardar</button
			>
			<button class="btn-primary mr-2 flex" on:click={() => addUser()}>
				<i class="fa fa-plus mt-1 mr-2" />
				Agregar Nuevo Usuario</button
			>

		</div>

		<!-- <div class="flex">
			<a
				href="https://cinva.cityciudad.com/cinva-control/api/api-Control.php?ref=download&archivo=2"
				target="_blank"
			>
				<button class="btn-primary mr-4 flex">
					<i class="fa fa-download mt-1 mr-2" />
					Descargar Excel</button
				></a
			>

			{#if uploadExcel}
				<Moon size="40" unit="px" duration="4s" />
			{/if}

			<button
				class="btn-primary px-2 text-xs"
				on:click={() => {
					file.click();
				}}
			>
				<i class="fa fa-upload mx-2" /> Subir Excel
			</button>

			<input
				type="file"
				accept=".xlsx"
				class="inputA w-32 hidden"
				placeholder="Subir Excel"
				bind:this={file}
				bind:files={fileExcel}
				on:change={() => {
					uploadExcel = true;
					upload();
				}}
			/>
		</div> -->


		<table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
			<thead class="text-xs text-white uppercase bg-primary dark:bg-gray-700 dark:text-gray-400">
				<th scope="col" class="" />
				<th scope="col" class=""> Nombre </th>
				<th scope="col" class=""> Documento </th>
				<th scope="col" class=""> Email </th>
				<th scope="col" class=""> Clave </th>
				<th scope="col" class=""> Tipo </th>
				<th scope="col" class=""> Activo </th>
			</thead>
			<tbody>
				{#each listUsers as user, i}
					<tr class="bg-white border-b hover:bg-aliceblue align-top">
						<td class="font-bold">{i + 1}</td>
						<td>
							<input
								type="text"
								class="inputA mr-2"
								bind:value={user.nombre}
								placeholder="nombre"
							/>
							
						</td>
      <td>
       <input type="text" class="inputA" bind:value={user.documento} placeholder="documento" />
      </td>
						<td>
							<input type="text" class="inputA" bind:value={user.email} placeholder="email" />
						</td>
						<td>
       {#if user.id < 1000000}
       <ControlNewPassword bind:m_show bind:message bind:userEdit_id={user.id} folder={'cinva_users'} />
      {/if}
						</td>
						<td>
							<select
								class="inputA"
								bind:value={user.tipo}
							>
<option value="administrador">Administrador</option>
<option value="vendedor">Vendedor</option>
<option value="operativo">Operativo</option>
<option value="diseñador">Diseñador</option>
      </select>

						</td>
						
						<td class="text-center"><input type="checkbox" bind:checked={user.activo} /></td>
					</tr>
				{:else}
					Sin registros
				{/each}
			</tbody>
		</table>
	</div>
</div>


{#if m_show == true}
	<Messages bind:m_show bind:message />
{/if}
