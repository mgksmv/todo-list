export type LengthAwarePaginator = {
  current_page: number;
  from: number;
  to: number;
  per_page: number;
  last_page: number;
  total: number;
  first_page_url: string;
  last_page_url: string;
  next_page_url: number | null;
  prev_page_url: string | null;
  path: string;
};
