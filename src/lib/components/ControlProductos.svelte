<script lang="ts">
	import type { Product } from '$lib/types/Product';
	import type { Proveedor } from '$lib/types/Proveedor';
	import { onMount } from 'svelte/internal';
	import { apiKey, userNow } from '../../store';

	import ControlProducto from '$lib/components/ControlProducto.svelte';

	import type { Message } from '$lib/types/Message';
	import { formattedNumber } from '$lib/utilities/FormatNumber';

	export let m_show: boolean;
	export let message: Message;

	//
	export let show_products: boolean;
	export let category_id: number = 0;
	export let category_name: string ='';

	//export let edit_product: Product
	let listProveedores: Proveedor[]
	let new_product: Product;
	let productN: Product = {
		id: 0,
		category_id: category_id,
		proveedor_id: 0,
		product: '',
		ref: '',
		description: '',
		description2: '',
		price: 0,
		size: '',
		color: '',
		stock: 0,
		unidad: 'unidad',
		image1: '',
		image2: '',
		image3: '',
		image4: '',
		position: 1,
		options: '',
		home: false,
		active: true,
		variants: []
	};
	new_product = productN;

	let product_list: Array<Product> = [];
	let product_edit: number;

	const urlAPI = $apiKey.urlAPI;

	const loadProducts= async (cat:number) => {
		console.log(
			urlAPI +
				'?ref=product-list&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&category_id=' +
				cat
		)
		await fetch(
			urlAPI +
				'?ref=product-list&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&category_id=' +
				cat
		)
			.then((response) => response.json())
			.then((result) => {
				//console.log("Listado Product Muy Bien:");
					product_list = result;				
			})
			.catch((error) => console.log(error));
	}

	const loadProveedores= async () => {
		console.log(
			urlAPI +
				'?ref=load-list&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=cinva_proveedores&campo=activo&campoV=1&orden=nombre'
		)
		await fetch(
			urlAPI +
				'?ref=load-list&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=cinva_proveedores&campo=activo&campoV=1&orden=nombre'
		)
			.then((response) => response.json())
			.then((result) => {
				listProveedores = result;				
			})
			.catch((error) => console.log(error));
	}

	$: loadProducts(category_id);
	
	const saveProduct = async () => {
		console.log("lista",product_list);
				//await fetch(urlAPI + '?ref=save-product', {
		await fetch(urlAPI + '?ref=save-list', {	
			method: 'POST', //POST - PUT - DELETE
			body: JSON.stringify({
				user_id: $userNow.id,
				time_life: $userNow.user_time_life,
				token: $userNow.token,
				list: product_list,
				folder: 'cinva_products',
				campo: 'category_id',
				campoV: category_id
			}),
			headers: {
				'Content-type': 'application/json; charset=UTF-8'
			}
		})
			.then((response) => response.json())
			//.then(result => console.log(result))
			.then((result) => {
				console.log('ok:'+result[0])
				 
					message = {
						title: 'Guardar',
						text: 'Se han guardado los datos',
						class: 'message-green',
						accion: ''
					};
					m_show = true;
					//product_list = result;					
					show_products = false;
				
			})
			.catch((error)=>{
				message = {
						title: 'Error',
						text: 'Error: ' + error,
						class: 'message-red',
						accion: ''
					};
					m_show = true;
			})
			
		//  });
	};

	//$: console.log(product_list);

	function add_product() {
		//product_list.push(new_product) ///esta opción no actuaiza el listado automáticamente
		new_product.id = Date.now();
		new_product.position = product_list.length + 1;
		//product_list.push(new_product)
		product_list = [...product_list, new_product];
		new_product = {
		id: 0,
		category_id: category_id,
		proveedor_id: 0,
		product: '',
		ref: '',
		description: '',
		description2: '',
		price: 0,
		size: '',
		color: '',
		stock: 0,
		unidad: 'unidades',
		image1: '',
		image2: '',
		image3: '',
		image4: '',
		position: 1,
		options: '',
		home: false,
		active: true,
		variants: []
	}
		//console.log('nuevo')
		//show_message("Add Product", "Information was saved", "message-green");
	}

	const deleteProduct = (id: number) => {
		if (confirm('Borrar this product?')) {
			product_list = product_list.filter((item) => item.id != id);
			//mensaje("se borró la tarea", "text-bg-danger");
			message = {
				title: 'Borrar',
				text: 'Se borró el producto',
				class: 'message-red',
				accion: ''
			};
			m_show = true;
		}

		//return product_list;
	};

	/// Edit Cont
	let show_product: boolean = false;
	let prod_position: number;
	
	onMount(()=>{
		loadProveedores()
	})
</script>

<svelte:head>
	<title>{category_name}</title>
</svelte:head>

<div class="px-3 w-full mt-0 ">
	<h3>Categoría: {category_name}</h3>

	<div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-2">
		<div class="flex">
			<button
				class="btn-primary flex mr-2"
				on:click={() => {
					show_products = false;
				}}
			>
				<i class="fa fa-angle-double-left mr-2 mt-1 " />
				<strong> Regresar </strong>
			</button>
			<button class="btn-green mr-2 flex" on:click={saveProduct}>
				<i class="fa fa-save mr-2 mt-1 " />
				Guardar</button
			>
			<button class="btn-primary flex" on:click={add_product}>
				<i class="fa fa-plus mr-2 mt-1 " />
				Agregar Nuevo Producto</button
			>
		</div>
		<table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
			<thead class="text-xs text-white uppercase bg-primary dark:bg-gray-700 dark:text-gray-400">
				<th scope="col" class="px-4 py-2" />
				<th scope="col" class="px-4 py-2"> Producto</th>

				<th scope="col" class="px-4 py-2"> Ref </th>
				<th scope="col" class="px-4 py-2"> Costo </th>
				<th scope="col" class="px-4 py-2"> Existencias </th>
				<th scope="col" class="px-4 py-2"> Posición </th>
				<th scope="col" class="px-4 py-2"> Home </th>
				<th scope="col" class="px-4 py-2"> Activo </th>
				<th scope="col" class="px-4 py-2" />
				<th scope="col" class="px-4 py-2" />
			</thead>
			<tbody>
				{#each product_list as ct, i}
					<tr class="bg-white border-b hover:bg-aliceblue">
						<td class="text-bold">{i + 1}</td>
						<td>
							<input type="text" class="inputA" bind:value={ct.product} />
						</td>
						<td>
							<input type="text" class="inputA" bind:value={ct.ref} />
						</td>
						<td class="text-center">
							<input type="number" min="0" class="inputA w-24" bind:value={ct.price} />
						</td>
						<td class="text-center">
							<input type="number" min="0" class="inputA w-24" bind:value={ct.stock} />
						</td>
						<td class="text-center">
							<input type="number" min="1" max="99" class="inputA w-12" bind:value={ct.position} />
						</td>
<td class="text-center"><input type="checkbox" bind:checked={ct.home} /></td>
						<td class="text-center"><input type="checkbox" bind:checked={ct.active} /></td>

						<td>
							<button
								on:click={() => {
									if (ct.id > 1000000) {
										message = {
											title: 'Error',
											text: 'Primero haga click en el botón de Guardar productos',
											class: 'message-red',
											accion: ''
										};
										m_show = true;
									} else {
										show_product = true;
										prod_position = i;
										//contProduct(i);
									}
								}}
							>
								<svg
									xmlns="http://www.w3.org/2000/svg"
									class="h-5 w-5 text-green cursor-pointer hover:black"
									fill="none"
									viewBox="0 0 24 24"
									stroke="currentColor"
									stroke-width="2"
								>
									<path
										stroke-linecap="round"
										stroke-linejoin="round"
										d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
									/>
								</svg>
							</button>
						</td>
						<td>
							<button
								on:click={() => {
									deleteProduct(ct.id);
								}}
							>
								<svg
									xmlns="http://www.w3.org/2000/svg"
									class="h-5 w-5 text-red cursor-pointer hover:black"
									fill="none"
									viewBox="0 0 24 24"
									stroke="currentColor"
									stroke-width="2"
								>
									<path
										stroke-linecap="round"
										stroke-linejoin="round"
										d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
									/></svg
								>
							</button>
						</td>
					</tr>

					<!---->
				{:else}
					Agrega un producto
				{/each}
			</tbody>
		</table>
	</div>
</div>

{#if show_product == true}
	<ControlProducto bind:show_product bind:prod={product_list[prod_position]} bind:m_show bind:message {listProveedores} />
{/if}
