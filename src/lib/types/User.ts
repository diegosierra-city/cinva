export interface User {
    id: number
    nombre: string
    documento:string
	email: string
    tipo: string
    permisos: string
	user_time_life: number
    token: string
    activo: boolean
}