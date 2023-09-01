<script lang="ts">
	import { apiKey, userNow } from '../../store';
	import { Circle3 } from 'svelte-loading-spinners';
	import type { Cliente } from '$lib/types/Cliente';
	import type { Maquina } from '$lib/types/Maquina';
	import type { Tecnico } from '$lib/types/Tecnico';
	import type { Visita } from '$lib/types/Visita';
	import { onMount } from 'svelte/internal';
	import type { Message } from '$lib/types/Message';
	import type { Insumo } from '$lib/types/Insumo';

	export let m_show: boolean = false;
	export let message;

	export let showVisita: boolean;
	export let listMaquinas: Array<Maquina> = [];
	export let listClientes: Array<Cliente> = [];
	//export let listTecnicos: Array<Tecnico> = [];
	export let listProgramacion: Array<Visita> = [];
	export let posicionReporte: number;

	export let reporte: Visita = {
		id: 0,
		cliente_id: 0,
		numero: 0,
		tecnico_id: 0,
		maquina_id: 0,
		fecha_solicitud: '',
		fecha_programada: '',
		hora_estimada: '',
		fecha_servicio: '',
		hora_inicio: '',
		hora_final: '',
		fecha_reporte: '',
		descripcion_trabajo: '',
		tipo_servicio: '',
		reporte: '',
		foto1: '',
		foto2: '',
		contador_negro: 0,
		contador_color: 0,
		valor: 0,
		estado: '',
		fecha_pago: ''
	};

	reporte = listProgramacion[posicionReporte];

	let listInsumos: Array<Insumo> = [];
	let newInsumo: Insumo = {
		id: 0,
		nombre: '',
		cantidad: 0,
		valor: 0,
		total: 0,
		position: 0
	};

	const urlAPI = $apiKey.urlAPI;
	const urlFiles = $apiKey.urlFiles;

	const loadInsumos = async (id: number) => {
		console.log(
			urlAPI +
				'?ref=load-list&folder=cst-&id=' +
				id +
				'&user_id=' +
				$userNow.id +
				'&time=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token
		);

		await fetch(
			urlAPI +
				'?ref=loadID&folder=maker_content_blocks&id=' +
				id +
				'&user_id=' +
				$userNow.id +
				'&time=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token
		)
			.then((response) => response.json())
			.then((result) => {
				console.log('ListadoInsumos:');
				console.table(result);
				listInsumos = result;
			})
			.catch((error) => console.log(error.message));
	};

	function addInsumo() {
		//menu_list.push(new_menu) ///esta opción no actuaiza el listado automáticamente
		newInsumo.id = Date.now();
		newInsumo.position = listInsumos.length + 1;
		//menu_list.push(new_menu)
		listInsumos = [...listInsumos, newInsumo];
		newInsumo = {
		id: 0,
		nombre: '',
		cantidad: 0,
		valor: 0,
		total: 0,
		position: 0
		};
	}

	const saveReporte = async (id: number) => {
		await fetch(urlAPI + '?ref=save', {
			method: 'POST', //POST - PUT - DELETE
			body: JSON.stringify({
				user_id: $userNow.id,
				time_life: $userNow.user_time_life,
				token: $userNow.token,
				cont_id: id,
				request: reporte,
				folder: 'maker_content_blocks'
				//
			}),
			headers: {
				'Content-type': 'application/json; charset=UTF-8'
			}
		})
			.then((response) => response.json())
			.then((result) => {
				console.log('save cont');
				console.log(result);
				//content = result[0];

				message = {
					title: 'Guardar',
					text: 'Se han guardado los datos',
					class: 'message-green',
					accion: ''
				};
				m_show = true;
				showVisita = false;
			})
			.catch((error) => console.error(error));
	};

	let fileImage: FileList;

	function upload(id: number, position: number) {
		const dataArray = new FormData();
		dataArray.append('user_id', String($userNow.id));
		dataArray.append('time_life', String($userNow.user_time_life));
		dataArray.append('token', $userNow.token);
		dataArray.append('id', String(id));
		dataArray.append('position', String(position));
		dataArray.append('uploadFile', fileImage[0]);

		fetch(urlAPI + '?ref=upload&folder=maker_pages&prefix=', {
			method: 'POST',
			body: dataArray
		})
			.then((response) => response.json())
			.then((result) => {
				// Successfully uploaded
				console.log('upload:');
				console.table(result[0]);
				reporte = result[0];
				message = {
					title: 'Cargar imagen',
					text: 'Imagen cargada con éxito',
					class: 'message-green',
					accion: ''
				};
				m_show = true;
			})
			.catch((error) => console.log(error.message));
	}

	const deleteInsumo = (id: number) => {
		if (confirm('Borrar this Item?')) {
			listInsumos = listInsumos.filter((item) => item.id != id);
			//mensaje("se borró la tarea", "text-bg-danger");
			message = {
				title: 'Borrar Item',
				text: 'Información borrada',
				class: 'message-red',
				accion: ''
			};
			m_show = true;

			saveInsumos();
		}

		//return menu_list;
	};

	const saveInsumos = async () => {
		//alert('FF')
		console.log('enviando gallery');

		await fetch(urlAPI + '?ref=save-gallery', {
			method: 'POST', //POST - PUT - DELETE
			body: JSON.stringify({
				user_id: $userNow.id,
				time_life: $userNow.user_time_life,
				token: $userNow.token,
				listGalleries: listInsumos
				//
			}),
			headers: {
				'Content-type': 'application/json; charset=UTF-8'
			}
		})
			.then((response) => response.json())
			.then((result) => {
				listInsumos = result;
			})
			.catch((error) => console.log(error.message));

		//  });
	};

	//$: console.log(content);
	let time = Date.now();

	import Editor from '@tinymce/tinymce-svelte';
	let conf = {
		toolbar: 'h1 h2 h3 bold  italic forecolor aligncenter alignjustify alignleft undo redo ',
		menubar: false,
		height: 200,
		width: '100%'
	};
</script>

<div class="bg-edit">
	<div class="edit-page">
		<h3 class="text-primary">Reporte Visita {reporte.id}</h3>

		<div class="flex mt-3 bg-aliceblue p-3 rounded-lg">
			<button
				class="btn-green flex"
				on:click={() => {
					saveReporte(reporte.id);
				}}
			>
				<i class="fa fa-save mx-2 mt-1" />
				Guardar</button
			>
			<button
				class="ml-4 flex btn-red"
				on:click={() => {
					showVisita = false;
				}}
			>
				<i class="fa fa-close mx-2 mt-1" />
				Cancel</button
			>
		</div>

		<div class="focus:outline-none xl:w-10/12 w-full">
			<!---->
			<div class="xl:px-8">
				<div class="mt-16 lg:flex justify-between border-b border-silver pb-4">
					<div class="w-80">
						<div class="flex items-center">
							<h1 class="focus:outline-none text-xl font-medium pr-2 leading-5 text-dimgray">
								Cliente - Máquina
							</h1>
						</div>
					</div>
					<div>
						<div class="md:flex items-start lg:ml-24">
							<div class="md:w-64 mx-2">
								<h4>{listClientes.find((cliente) => cliente.id === reporte.cliente_id)?.nombre}</h4>

								{listMaquinas.find((maquina) => maquina.id === reporte.maquina_id)?.direccion}
							</div>

							<div class="md:w-64 mx-2">
								<h4>Maquina:</h4>

								{listMaquinas.find((maquina) => maquina.id === reporte.maquina_id)
									?.marca}-{listMaquinas.find((maquina) => maquina.id === reporte.maquina_id)
									?.modelo}<br />
								{listMaquinas.find((maquina) => maquina.id === reporte.maquina_id)?.serial}<br />
								CN:
								<input
									type="text"
									class="inputA w-40"
									bind:value={reporte.contador_negro}
									placeholder="Contador Negro"
								/><br />
								CC:
								<input
									type="text"
									class="inputA w-40"
									bind:value={reporte.contador_color}
									placeholder="Contador Color"
								/><br />
							</div>
						</div>
					</div>
				</div>
			</div>

			<!---->

			<div class="xl:px-8">
				<div class="mt-16 lg:flex justify-between border-b border-silver pb-4">
					<div class="w-80">
						<div class="flex items-center">
							<h1 class="focus:outline-none text-xl font-medium pr-2 leading-5 text-dimgray">
								Fotos
							</h1>
						</div>
						<p class="focus:outline-none mt-4 text-sm leading-5 text-gray">Cargar Imagenes</p>
					</div>
					<div>
						<div class="md:flex items-center lg:ml-24">
							<div class="md:w-64 m-2">
								{#if reporte.foto1 == 'load'}
									<Circle3 size="60" unit="px" duration="2s" />
								{:else if reporte.foto1 != ''}
									<img
										src="{urlFiles}/images/reportes/M{reporte.foto1}"
										alt={'Reporte: ' + reporte.id}
									/> <br />
									<button
										class="btn-red flex"
										on:click={() => {
											reporte.foto1 = '';
										}}
									>
										<i class="fa fa-trash-o mx-2 mt-1" />
										Borrar
									</button>
								{/if}
								Image 1 JPG - PNG <br />

								<input
									type="file"
									accept=".jpg, .jpeg, .png"
									class="inputA"
									placeholder="Image 1"
									bind:files={fileImage}
									on:change={() => {
										reporte.foto1 = 'load';
										upload(reporte.id, 1);
									}}
								/>
							</div>

							<div class="md:w-64 mx-2">
								{#if reporte.foto2 == 'load'}
									<Circle3 size="60" unit="px" duration="2s" />
								{:else if reporte.foto2 != ''}
									<img
										src="{urlFiles}/images/reportes/M{reporte.foto2}"
										alt={'Reporte: ' + reporte.id}
									/> <br />
									<button
										class="btn-red flex"
										on:click={() => {
											reporte.foto2 = '';
										}}
									>
										<i class="fa fa-trash-o" />
										Borrar
									</button>
								{/if}
								Imagen 2 JPG - PNG <br />

								<input
									type="file"
									accept=".jpg, .jpeg, .png"
									class="inputA"
									placeholder="Image 2"
									bind:files={fileImage}
									on:change={() => {
										reporte.foto2 = 'load';
										upload(reporte.id, 2);
									}}
								/>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!---->

			<div class="xl:px-8">
				<div class="mt-16 lg:flex justify-between border-b border-silver pb-4">
					<div class="w-80">
						<div class="flex items-center">
							<h1 class="focus:outline-none text-xl font-medium pr-2 leading-5 text-dimgray">
								Datos
							</h1>
						</div>
						<p class="focus:outline-none mt-4 text-sm leading-5 text-gray" />
					</div>
					<div>
						<div class="md:flex items-center lg:ml-24">
							<div class="md:w-64 mx-2">
								Fecha Servicio:<br />
								<input
									type="text"
									class="inputA"
									placeholder="Title"
									bind:value={reporte.fecha_reporte}
								/>
							</div>

							<div class="md:w-64 mx-2">
								Hora <br />

								<input
									type="text"
									class="inputA w-24"
									placeholder="Title"
									bind:value={reporte.hora_inicio}
								/>
								-
								<input
									type="text"
									class="inputA w-24"
									placeholder="Title"
									bind:value={reporte.hora_final}
								/>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="xl:px-8">
				<div class="mt-16 lg:flex justify-between border-b border-silver pb-4">
					<div class="w-80">
						<div class="flex items-center">
							<h1 class="text-xl font-medium pr-2 leading-5 text-dimgray">Insumos</h1>
						</div>
						<p class="mt-4 text-sm leading-5 text-gray" />
						<div class="mt-4 text-sm leading-5 text-gray">
							<button class="btn-primary flex" on:click={addInsumo}>
								<i class="fa fa-plus mr-2 mt-1" />
								Agregar Insumo</button
							>
						</div>
					</div>

					{#if listInsumos.length > 0}
						<!--fields-->
						<div class="md:flex items-center w-8/12 ml-auto">
							<table>
								<thead>
									<th scope="col" class="px-2 py-1" />
									<th scope="col" class="px-2 py-1"> Insumo</th>
									<th scope="col" class="px-2 py-1"> Cantidad</th>
									<th scope="col" class="px-2 py-1"> Valor Un. </th>
									<th scope="col" class="px-2 py-1"> Valor </th>
									<th scope="col" class="px-4 py-1" />
								</thead>
								<tbody>
									{#each listInsumos as insumo, i}
										<tr class="bg-white border-b hover:bg-aliceblue">
											<td class="font-bold">{i + 1}</td>
											<td>
												<input
													type="text"
													class="inputA w-40"
													bind:value={insumo.nombre}
													placeholder="Insumo"
												/>
											</td>
											<td
												><input
													type="number"
													class="inputA w-20"
													placeholder="Cant"
													bind:value={insumo.cantidad}
												/></td
											>

											<td class="text-center">
												<input
													type="number"
													min="1"
													class="inputA w-20"
													bind:value={insumo.valor}
												/>
											</td>
											<td class="text-center">
												<input
													type="number"
													min="1"
													class="inputA w-20"
													bind:value={insumo.total}
												/>
											</td>

											<td>
												<button
													on:click={() => {
														deleteInsumo(insumo.id);
													}}
												>
													<i class="fa fa-trash-o mr-2 mt-1 text-red" />
												</button>
											</td>
										</tr>
									{/each}
								</tbody>
							</table>
						</div>
						<!--the end fields-->
					{/if}
				</div>
			</div>

			<div class="xl:px-8">
				<div class="mt-16 lg:flex justify-between border-b border-silver pb-4">
					<div class="w-80">
						<div class="flex items-center">
							<h1 class="focus:outline-none text-xl font-medium pr-2 leading-5 text-dimgray">
								Nota
							</h1>
						</div>
					</div>
					<div>
						<div class="md:flex items-center lg:ml-24">
							<div class="md:w-130 mx-2">
								<Editor
									apiKey="6omiyxavakt13jx418pdqk4jh453k7vgjz33blqckjrskk88"
									bind:value={reporte.descripcion_trabajo}
									{conf}
								/>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="flex mt-3 bg-aliceblue p-3 rounded-lg">
				<button
					class="btn-green flex"
					on:click={() => {
						showVisita = false;
						saveReporte(reporte.id);
					}}
				>
					<i class="fa fa-save mx-2 mt-1" />
					Guardar</button
				>
				<button
					class="ml-4 flex btn-red"
					on:click={() => {
						showVisita = false;
					}}
				>
					<i class="fa fa-close mx-2 mt-1" />
					Cancel</button
				>
			</div>
		</div>
	</div>
</div>
