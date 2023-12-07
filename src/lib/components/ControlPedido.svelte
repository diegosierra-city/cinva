<script lang="ts">
	import { onMount } from 'svelte';
	import { apiKey, userNow } from '../../store';
	import axios from 'axios'
	import type { Pedido, PedidoProduct } from '$lib/types/Pedido';
	import type { Product } from '$lib/types/Product';
	import type { Category } from '$lib/types/Category';

	import type { Proveedor } from '$lib/types/Proveedor';
	import type { Cliente } from '$lib/types/Cliente';
	import { Circle3 } from 'svelte-loading-spinners';
	//import Messages from '$lib/components/Messages.svelte';
	import type { ProductOptions } from '$lib/types/ProductOptions';
import { formattedNumber } from '$lib/utilities/FormatNumber';
	import type { Message } from '$lib/types/Message';

	export let m_show: boolean = false;
	export let message: Message;
	export let pedido: Pedido;
	export let listClientes: Cliente[];
	export let listCategorias: Category[];
	export let listProductos: Product[];
	export let show_pedido: boolean;
	export let listPedidos: Pedido[];

	const urlAPI = $apiKey.urlAPI;
	const urlFiles = $apiKey.urlFiles;

	const savePedido = async () => {
		console.log('guardando pedido', pedido);
		if (pedido.comprador_id === 0) {
			message = {
				title: 'Error',
				text: 'Error: No se ha definido el Cliente',
				class: 'message-red',
				accion: ''
			};
			m_show = true;
			return;
		}

		if (!listPedidoProductos[0] || listPedidoProductos[0].product_id === 0) {
			message = {
				title: 'Error',
				text: 'Error: No se tiene ningún producto',
				class: 'message-red',
				accion: ''
			};
			m_show = true;
			return;
		}
		try {
			const response = await fetch(urlAPI + '?ref=save', {
				method: 'POST', //POST - PUT - DELETE
				body: JSON.stringify({
					user_id: $userNow.id,
					time_life: $userNow.user_time_life,
					token: $userNow.token,
					request: pedido,
					folder: 'cinva_orders'
				}),
				headers: {
					'Content-type': 'application/json; charset=UTF-8'
				}
			});
			const result = await response.json();
			console.log('cargado', result[0]);

			message = {
				title: 'Guardar',
				text: 'Se guardó el pedido N.: ' + result[0].id,
				class: 'message-green',
				accion: ''
			};
			m_show = true;
			if (pedido.id === 0) {
				///agrego pedido al inicio del listado de listPedidos
				listPedidos = [result[0], ...listPedidos];
				/// en todos los objetos del listado de listPedidoProduct actualizamos el pedido_id
				const arrActualizado = listPedidoProductos.map((obj) => {
					if (obj.order_id === 0) {
						return { ...obj, order_id: result[0].id };
					} else {
						return obj;
					}
				});
				listPedidoProductos = [...arrActualizado];
				await savePedidoProductos();
			}
		} catch (error: any) {
			console.log(error.message);
		}
	};

	let fileImage: FileList;

	const upload = async (position: number) => {
		//console.table(fileImage[0]);
		const dataArray = new FormData();
		dataArray.append('user_id', String($userNow.id));
		dataArray.append('time_life', String($userNow.user_time_life));
		dataArray.append('token', $userNow.token);
		dataArray.append('id', String(pedido.id));
		dataArray.append('position', String(position));
		dataArray.append('uploadFile', fileImage[0]);

		await fetch(urlAPI + '?ref=upload&folder=cinva_orders&prefix=', {
			method: 'POST',
			body: dataArray
		})
			.then((response) => response.json())
			.then((result) => {
				// Successfully uploaded
				console.log('upload:', result);

				pedido = result[0];
				message = {
					title: 'Cargar',
					text: 'Imágen cargada',
					class: 'message-green',
					accion: ''
				};
				m_show = true;
			})
			.catch((error) => {
				message = {
					title: 'Error',
					text: 'Error: ' + error.message,
					class: 'message-red',
					accion: ''
				};
				m_show = true;
				console.log(error.message);
			});
	};

	let fileinput: any;
	let fileinputPosition: number = 0;

	let productNow: PedidoProduct;
	let listPedidoProductos: PedidoProduct[] = [];
	let dataCompany: any = {};

	const loadDataCompany = async () => {
		console.log();
		await fetch(
			urlAPI +
				'?ref=load&folder=cinva_config&id=1&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token
		)
			.then((response) => response.json())
			.then((result) => {
				console.log('options load:', result);
				dataCompany = result;
			})
			.catch((error) => console.log(error.message));
	};

	onMount(() => {
		//loadPedidoProducts(pedido.id);
		loadDataCompany();
	});

	const loadPedidoProducts = async (p: number) => {
		console.log('productos',p);
		console.log(
			urlAPI +
				'?ref=load-list&folder=cinva_order_products&campo=order_id&campoV=' +
				p +
				'&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token
		)
		try {
			const response = await axios.get(
			urlAPI +
				'?ref=load-list&folder=cinva_order_products&campo=order_id&campoV=' +
				p +
				'&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token
		)
		console.log('Lista de productos:', response.data);
				listPedidoProductos = response.data;
		} catch (error) {
			console.log(error)
		}
		
	};

	
	$:	loadPedidoProducts(pedido.id);


	function addProduct() {
		//menu_list.push(new_menu) ///esta opción no actuaiza el listado automáticamente
		productNow = {
			id: Date.now(),
			order_id: pedido.id,
			category_id: 0,
			product_id: 0,
			name: '',
			ref: '',
			image: '',
			description: '',
			description2: '',
			size: '',
			color: '',
			stock: 0,
			version_type: '',
			version: '',
			image_version: '',
			price: 0,
			quantity: 0,
			unit: '',
			total: 0,
			cod: 0
		};
		//
		listPedidoProductos = [...listPedidoProductos, productNow];
	}

	const deleteProduct = (id: number) => {
		if (confirm('Borrar este Producto?')) {
			listPedidoProductos = listPedidoProductos.filter((item) => item.id != id);
			//
			message = {
				title: 'Borrar',
				text: 'Producto Borrado',
				class: 'message-red',
				accion: ''
			};
			m_show = true;
		}

		//return menu_list;
	};

	const savePedidoProductos = () => {
		//alert('FF')
		console.log('enviando', listPedidoProductos);
		//fetch(urlAPI + '?ref=save-options', {
		fetch(urlAPI + '?ref=save-list', {
			method: 'POST', //POST - PUT - DELETE
			body: JSON.stringify({
				user_id: $userNow.id,
				time_life: $userNow.user_time_life,
				token: $userNow.token,
				list: listPedidoProductos,
				folder: 'cinva_order_products',
				campo: 'order_id',
				campoV: pedido.id
			}),
			headers: {
				'Content-type': 'application/json; charset=UTF-8'
			}
		})
			.then((response) => response.json())
			.then((result) => {
				console.log('ListProductos:', result);
				show_pedido = false;
			})
			.catch((error) => {
				console.log(error);
			});
	};

	import Editor from '@tinymce/tinymce-svelte';
	let conf = {
		toolbar: 'h1 h2 h3 bold  italic forecolor aligncenter alignjustify alignleft undo redo ',
		menubar: false,
		height: 200,
		width: '100%'
	};

	const updateDataProd = (p: PedidoProduct, i: number) => {
		console.log('seleccionadoProd:',p)
		let valor: number = Number(p.price);
		let cantidad: number = Number(p.quantity);
		//if(cantidad!> 0) cantidad = 1
		//
		if (cantidad > p.stock) {
			message = {
				title: 'Error',
				text: `Sólo hay disponibles ${p.quantity} `,
				class: 'message-red',
				accion: ''
			};
			m_show = true;
			listPedidoProductos[i] = { ...listPedidoProductos[i], quantity: p.stock };
			return;
		}

		let total: number = cantidad * valor;
		///se actualiza el objeto i del listado con estos valores
		console.log('total:', total, valor, cantidad, cantidad * valor);
		listPedidoProductos[i] = { ...listPedidoProductos[i], total: total };
		totalizar();
	};

	const selectProd = (i: number) => {
		let p: Product = listProductos.find(
			(p) => p.id == listPedidoProductos[i].product_id
		) as Product;
		//console.log('seleccionado:', p);
		//console.log('Calculo precio:',Number(p.price),Number(dataCompany.porcentaje_ganancia))

		listPedidoProductos[i] = {
			...listPedidoProductos[i],
			category_id: p.category_id,
			name: p.product,
			ref: p.ref,
			image: p.image1,
			description: p.description,
			description2: p.description2,
			size: p.size,
			color: p.color,
			stock: p.stock,
			price: Math.ceil((Number(p.price) * (Number(dataCompany.porcentaje_ganancia)+100)) / 100),
			quantity: 1,
			unit: p.unidad
		};
		updateDataProd(listPedidoProductos[i], i);
	};

	let total_final: number = 0;
	let descuento_final: number = 0;

	const totalizar = () => {
		let total = 0;

		for (let obj of listPedidoProductos) {
			total += obj.price * obj.quantity;
		}
		if (pedido.descuento_porcentaje > 0) {
			descuento_final = Math.ceil((total * pedido.descuento_porcentaje) / 100);
		} else if (pedido.descuento_valor > 0) {
			descuento_final = pedido.descuento_valor;
		}


		pedido.valor = total;
		total_final = total - descuento_final;
		if (total_final < 0) total_final = 0;
		pedido.total = total_final;
	};

</script>

<!-- svelte-ignore a11y-click-events-have-key-events -->
<div class="bg-edit" on:click|self={() => (show_pedido = false)}>
	<div class="edit-page">
		<h3>
			{#if pedido.id !== 0}
				Pedido N.{pedido.id}
			{:else}
				Pedido Nuevo
			{/if}
		</h3>

		<div class="flex mt-3 bg-aliceblue p-3 rounded-lg">
			<button
				class="btn-green flex"
				on:click={() => {
					savePedido();
				}}
			>
				<i class="fa fa-save mr-1 mt-1" />
				Guardar</button
			>
			<button
				class="ml-4 flex btn-red"
				on:click={() => {
					show_pedido = false;
				}}
			>
				<i class="fa fa-close mr-1 mt-1" />
				Cerrar</button
			>
		</div>

		<div class="xl:w-10/12 w-full">
			<div class="xl:px-8">
				<div class="mt-2 lg:flex justify-between border-b border-silver pb-4">
					<div>
						<div class="md:flex items-center lg:ml-24">
							<div class="md:w-64 mx-2">
								Cliente <br />
								{#if pedido.id === 0}
								{#if listClientes.length > 0}
								<!-- content here -->
								<select class="inputA" bind:value={pedido.comprador_id}>
									<option value={0}>Seleccione</option>
									{#each listClientes as cliente}
										<!-- content here -->
										<option value={cliente.id}>{cliente.nombre} - {cliente.ciudad}</option>
									{/each}
								</select>
							{/if}
								{:else}
									 <!-- muestro el objeto del listClientes que tenga el id igual al comprador_id -->
										{listClientes?.find(c => c.id === pedido.comprador_id)?.nombre}
								{/if}

								
							</div>

							<div class="md:w-64 mx-2">
								Descuento % <br />
								{#if pedido.id === 0}
								<input
									type="number"
									class="inputA"
									placeholder="Descuento %"
									bind:value={pedido.descuento_porcentaje}
									on:input={() => {
										pedido.descuento_valor = 0;
										totalizar();
									}}
								/>
								{:else}
								{pedido.descuento_porcentaje}
								{/if}
							</div>

							<div class="md:w-64 mx-2">
								Descuento $ <br />
								{#if pedido.id === 0}
								<input
									type="text"
									class="inputA"
									placeholder="Descuento $"
									bind:value={pedido.descuento_valor}
									on:input={() => {
										pedido.descuento_porcentaje = 0;
										totalizar();
									}}
								/>
								{:else}
								{pedido.descuento_valor}
								{/if}
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="xl:px-8">
				<div class="mt-2 border-b border-silver pb-4">
					{#if pedido.id === 0}
					<button class="btn-primary flex" on:click={addProduct}>
						<i class="fa fa-plus mr-2 mt-1" />
						Agregar Producto</button
					>
					{/if}

					{#if listPedidoProductos?.length > 0}
						<!--fields-->
						<div class="md:flex items-center">
							<table>
								<thead>
									<th scope="col" class="px-2 py-1" />
									<th scope="col" class="px-2 py-1"> Producto</th>
									{#if pedido.id === 0}
									<th scope="col" class="px-2 py-1"> Inventario </th>
									{/if}
									<th scope="col" class="px-2 py-1"> Valor Unitario</th>
									<th scope="col" class="px-2 py-1"> Cantidad </th>
									<th scope="col" class="px-2 py-1"> Total </th>
									{#if pedido.id === 0}
									<th scope="col" class="px-4 py-1" />
									{/if}
								</thead>
								<tbody>
									{#each listPedidoProductos as prod, i}
										<tr class="bg-white border-b hover:bg-aliceblue">
											<td class="font-bold">{i + 1}</td>
											<td>
												{#if pedido.id === 0}
												<select
													class="inputA"
													bind:value={prod.product_id}
													on:change={() => selectProd(i)}
												>
													<option value={0}>Seleccione </option>
													{#each listCategorias as cat}
														<optgroup class="font-bold" label={cat.category}>
															{#each listProductos.filter((p) => p.category_id === cat.id) as p}
																<option value={p.id}>{p.product} ({p.stock})</option>
															{/each}
														</optgroup>{/each}
												</select>
												{:else}
								{prod.name}
								{/if}
											</td>
											{#if pedido.id === 0}
											<td class="text-center">
												{prod.stock}
											</td>
											{/if}
											<td class="text-right">
												{formattedNumber(prod.price)}
											</td>
											<td class="text-center">
												{#if pedido.id === 0}
												<input
													type="number"
													min="1"
													class="inputA w-12"
													bind:value={prod.quantity}
													on:change={() => updateDataProd(prod, i)}
												/>
												{:else}
{prod.quantity}
												{/if}
												{prod.unit}
											</td>
											<td class="text-right">
												{formattedNumber(prod.total)}
											</td>

											{#if pedido.id === 0}
											<td>
												<button
													on:click={() => {
														deleteProduct(prod.id);
													}}
												>
													<i class="fa fa-trash-o mr-2 mt-1 text-red" />
												</button>
											</td>
{/if}
										</tr>
									{/each}
									<tr>
										<td />
										<td colspan={pedido.id === 0? 4 : 3} class="text-right"> Total</td>
										<td class="text-right"> {formattedNumber(pedido.valor)} </td>
										{#if pedido.id === 0}
										<td />
										{/if}
									</tr>
									{#if descuento_final > 0}
										<tr>
											<td />
											<td colspan={pedido.id === 0? 4 : 3} class="text-right"> Descuentos</td>
											<td class="text-right"> {formattedNumber(descuento_final)} </td>
											{#if pedido.id === 0}
											<td />
											{/if}
										</tr>
									{/if}

									<tr>
										<td />
										<td colspan={pedido.id === 0? 4 : 3} class="text-right"> Total a Pagar</td>
										<td class="text-right"> {pedido.id === 0?  formattedNumber(total_final) : formattedNumber(pedido.total)} </td>
										{#if pedido.id === 0}
										<td />
										{/if}
									</tr>
								</tbody>
							</table>
						</div>
						<!--the end fields-->
					{/if}
				</div>
			</div>

			<div class="xl:px-8">
				<div class="mt-16 lg:flex justify-between border-b border-silver pb-4">
					<div>
						<div class="md:flex items-center lg:ml-24">
							<div class="w-full mx-2">
								Nota<br />
								<Editor
									apiKey="6omiyxavakt13jx418pdqk4jh453k7vgjz33blqckjrskk88"
									bind:value={pedido.notas}
									{conf}
								/>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="flex mt-3 bg-aliceblue p-3 rounded-lg">
			<button
				class="btn-green flex"
				on:click={() => {
					savePedido();
				}}
			>
				<i class="fa fa-save mr-2 mt-1" />
				Guardar</button
			>
			<button
				class="ml-4 flex btn-red"
				on:click={() => {
					show_pedido = false;
				}}
			>
				<i class="fa fa-close mr-2 mt-1" />
				Cerrar</button
			>
		</div>
	</div>
</div>
