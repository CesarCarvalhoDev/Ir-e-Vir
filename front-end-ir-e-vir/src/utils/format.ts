export const money = (value: number | string | null | undefined) => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(value ?? 0))
export const dateTime = (value: string | null | undefined) => value ? new Intl.DateTimeFormat('pt-BR', { dateStyle: 'short', timeStyle: 'short' }).format(new Date(value)) : 'Não informado'
export const localDateTimeValue = () => { const now = new Date(); now.setMinutes(now.getMinutes() - now.getTimezoneOffset()); return now.toISOString().slice(0, 16) }
const statusLabels: Record<string, string> = { ACTIVE: 'Ativa', FINISHED: 'Finalizada', IRREGULAR: 'Irregular', PENDING: 'Pendente', PAID: 'Paga', OVERDUE: 'Vencida', active: 'Ativa', paid: 'Paga', resolved: 'Resolvida', canceled: 'Cancelada', completed: 'Concluído', failed: 'Falhou', pending: 'Pendente' }
export const statusLabel = (status: string) => statusLabels[status] ?? status
const reasonLabels: Record<string, string> = { insufficient_balance: 'Saldo insuficiente', time_limit_exceeded: 'Tempo máximo excedido' }
export const reasonLabel = (reason: string) => reasonLabels[reason] ?? reason
