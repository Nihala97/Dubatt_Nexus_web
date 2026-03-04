/**
 * offline-db.js
 * IndexedDB wrapper for Receiving, Acid Testing, BBSU
 * ─────────────────────────────────────────────────────
 * Stores:
 *   - records      : actual form data per module
 *   - sync_queue   : pending / failed sync jobs
 *   - cached_lists : dropdown / prefill data cached from API
 */

const DB_NAME    = 'mes_offline_db';
const DB_VERSION = 1;

const STORES = {
  RECORDS      : 'records',
  SYNC_QUEUE   : 'sync_queue',
  CACHED_LISTS : 'cached_lists',
};

const MODULES = {
  RECEIVING    : 'receiving',
  ACID_TESTING : 'acid_testing',
  BBSU         : 'bbsu',
};

const SYNC_STATUS = {
  PENDING : 'pending',
  SYNCING : 'syncing',
  SYNCED  : 'synced',
  FAILED  : 'failed',
};

class OfflineDB {
  constructor() {
    this.db = null;
  }

  // ── Open / Init ────────────────────────────────────────────────
  async open() {
    if (this.db) return this.db;

    return new Promise((resolve, reject) => {
      const req = indexedDB.open(DB_NAME, DB_VERSION);

      req.onupgradeneeded = (e) => {
        const db = e.target.result;

        // records store: keyed by local UUID
        if (!db.objectStoreNames.contains(STORES.RECORDS)) {
          const store = db.createObjectStore(STORES.RECORDS, { keyPath: 'local_id' });
          store.createIndex('module',      'module',      { unique: false });
          store.createIndex('status',      'status',      { unique: false });
          store.createIndex('sync_status', 'sync_status', { unique: false });
          store.createIndex('module_sync', ['module','sync_status'], { unique: false });
        }

        // sync_queue store
        if (!db.objectStoreNames.contains(STORES.SYNC_QUEUE)) {
          const sq = db.createObjectStore(STORES.SYNC_QUEUE, { keyPath: 'queue_id', autoIncrement: true });
          sq.createIndex('local_id',   'local_id',   { unique: false });
          sq.createIndex('sync_status','sync_status',{ unique: false });
          sq.createIndex('module',     'module',     { unique: false });
        }

        // cached_lists store: key = "module:list_name"
        if (!db.objectStoreNames.contains(STORES.CACHED_LISTS)) {
          db.createObjectStore(STORES.CACHED_LISTS, { keyPath: 'cache_key' });
        }
      };

      req.onsuccess = (e) => { this.db = e.target.result; resolve(this.db); };
      req.onerror   = (e) => reject(e.target.error);
    });
  }

  // ── Generic transaction helpers ────────────────────────────────
  async _tx(storeName, mode, fn) {
    await this.open();
    return new Promise((resolve, reject) => {
      const tx    = this.db.transaction(storeName, mode);
      const store = tx.objectStore(storeName);
      const req   = fn(store);
      req.onsuccess = (e) => resolve(e.target.result);
      req.onerror   = (e) => reject(e.target.error);
    });
  }

  async _txAll(storeName, mode, fn) {
    await this.open();
    return new Promise((resolve, reject) => {
      const tx    = this.db.transaction(storeName, mode);
      const store = tx.objectStore(storeName);
      const results = [];
      const cursor  = fn(store);
      cursor.onsuccess = (e) => {
        const c = e.target.result;
        if (c) { results.push(c.value); c.continue(); }
        else resolve(results);
      };
      cursor.onerror = (e) => reject(e.target.error);
    });
  }

  // ── Records ────────────────────────────────────────────────────

  async saveRecord(module, formData, serverId = null) {
    const local_id   = formData.local_id || generateUUID();
    const now        = new Date().toISOString();
    const sync_status = serverId ? SYNC_STATUS.SYNCED : SYNC_STATUS.PENDING;

    const record = {
      ...formData,
      local_id,
      module,
      server_id   : serverId,
      sync_status,
      created_at  : formData.created_at || now,
      updated_at  : now,
    };

    await this._tx(STORES.RECORDS, 'readwrite', (store) => store.put(record));
    return record;
  }

  async getRecord(local_id) {
    return this._tx(STORES.RECORDS, 'readonly', (store) => store.get(local_id));
  }

  async getRecordsByModule(module) {
    await this.open();
    return new Promise((resolve, reject) => {
      const tx      = this.db.transaction(STORES.RECORDS, 'readonly');
      const store   = tx.objectStore(STORES.RECORDS);
      const index   = store.index('module');
      const results = [];
      const req     = index.openCursor(IDBKeyRange.only(module));
      req.onsuccess = (e) => {
        const c = e.target.result;
        if (c) { results.push(c.value); c.continue(); }
        else resolve(results.sort((a,b) => new Date(b.updated_at) - new Date(a.updated_at)));
      };
      req.onerror = (e) => reject(e.target.error);
    });
  }

  async getPendingRecords() {
    await this.open();
    return new Promise((resolve, reject) => {
      const tx      = this.db.transaction(STORES.RECORDS, 'readonly');
      const store   = tx.objectStore(STORES.RECORDS);
      const index   = store.index('sync_status');
      const results = [];
      const req     = index.openCursor(IDBKeyRange.only(SYNC_STATUS.PENDING));
      req.onsuccess = (e) => {
        const c = e.target.result;
        if (c) { results.push(c.value); c.continue(); }
        else resolve(results);
      };
      req.onerror = (e) => reject(e.target.error);
    });
  }

  async getFailedRecords() {
    await this.open();
    return new Promise((resolve, reject) => {
      const tx      = this.db.transaction(STORES.RECORDS, 'readonly');
      const store   = tx.objectStore(STORES.RECORDS);
      const index   = store.index('sync_status');
      const results = [];
      const req     = index.openCursor(IDBKeyRange.only(SYNC_STATUS.FAILED));
      req.onsuccess = (e) => {
        const c = e.target.result;
        if (c) { results.push(c.value); c.continue(); }
        else resolve(results);
      };
      req.onerror = (e) => reject(e.target.error);
    });
  }

  async updateRecordSyncStatus(local_id, sync_status, server_id = null, error = null) {
    await this.open();
    return new Promise((resolve, reject) => {
      const tx    = this.db.transaction(STORES.RECORDS, 'readwrite');
      const store = tx.objectStore(STORES.RECORDS);
      const getReq = store.get(local_id);
      getReq.onsuccess = (e) => {
        const record = e.target.result;
        if (!record) { resolve(null); return; }
        record.sync_status = sync_status;
        record.updated_at  = new Date().toISOString();
        if (server_id) record.server_id = server_id;
        if (error)     record.sync_error = error;
        else           delete record.sync_error;
        const putReq = store.put(record);
        putReq.onsuccess = () => resolve(record);
        putReq.onerror   = (e) => reject(e.target.error);
      };
      getReq.onerror = (e) => reject(e.target.error);
    });
  }

  async deleteRecord(local_id) {
    return this._tx(STORES.RECORDS, 'readwrite', (store) => store.delete(local_id));
  }

  // ── Sync Queue ─────────────────────────────────────────────────

  async enqueue(module, local_id, method, endpoint, payload) {
    const job = {
      module,
      local_id,
      method,
      endpoint,
      payload,
      sync_status  : SYNC_STATUS.PENDING,
      attempts     : 0,
      max_attempts : 3,
      created_at   : new Date().toISOString(),
      last_attempt : null,
      error        : null,
    };
    return this._tx(STORES.SYNC_QUEUE, 'readwrite', (store) => store.add(job));
  }

  async getPendingQueue() {
    await this.open();
    return new Promise((resolve, reject) => {
      const tx      = this.db.transaction(STORES.SYNC_QUEUE, 'readonly');
      const store   = tx.objectStore(STORES.SYNC_QUEUE);
      const index   = store.index('sync_status');
      const results = [];
      const req     = index.openCursor(IDBKeyRange.only(SYNC_STATUS.PENDING));
      req.onsuccess = (e) => {
        const c = e.target.result;
        if (c) { results.push(c.value); c.continue(); }
        else resolve(results);
      };
      req.onerror = (e) => reject(e.target.error);
    });
  }

  async updateQueueJob(queue_id, updates) {
    await this.open();
    return new Promise((resolve, reject) => {
      const tx    = this.db.transaction(STORES.SYNC_QUEUE, 'readwrite');
      const store = tx.objectStore(STORES.SYNC_QUEUE);
      const getReq = store.get(queue_id);
      getReq.onsuccess = (e) => {
        const job = e.target.result;
        if (!job) { resolve(null); return; }
        Object.assign(job, updates);
        const putReq = store.put(job);
        putReq.onsuccess = () => resolve(job);
        putReq.onerror   = (e) => reject(e.target.error);
      };
      getReq.onerror = (e) => reject(e.target.error);
    });
  }

  async removeQueueJob(queue_id) {
    return this._tx(STORES.SYNC_QUEUE, 'readwrite', (store) => store.delete(queue_id));
  }

  // ── Cached Lists ───────────────────────────────────────────────

  async cacheList(key, data, ttl_minutes = 60) {
    const entry = {
      cache_key  : key,
      data,
      cached_at  : new Date().toISOString(),
      expires_at : new Date(Date.now() + ttl_minutes * 60 * 1000).toISOString(),
    };
    return this._tx(STORES.CACHED_LISTS, 'readwrite', (store) => store.put(entry));
  }

  async getCachedList(key) {
    const entry = await this._tx(STORES.CACHED_LISTS, 'readonly', (store) => store.get(key));
    if (!entry) return null;
    if (new Date(entry.expires_at) < new Date()) return null; // expired
    return entry.data;
  }

  // ── Stats ──────────────────────────────────────────────────────

  async getStats() {
    const [pending, failed, all] = await Promise.all([
      this.getPendingRecords(),
      this.getFailedRecords(),
      this.getRecordsByModule(MODULES.RECEIVING).then(r =>
        this.getRecordsByModule(MODULES.ACID_TESTING).then(a =>
          this.getRecordsByModule(MODULES.BBSU).then(b => [...r, ...a, ...b])
        )
      ),
    ]);
    return {
      total   : all.length,
      pending : pending.length,
      failed  : failed.length,
      synced  : all.filter(r => r.sync_status === SYNC_STATUS.SYNCED).length,
    };
  }
}
function generateUUID() {
  return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
      const r = Math.random() * 16 | 0;
      const v = c === 'x' ? r : (r & 0x3 | 0x8);
      return v.toString(16);
  });
}

// Singleton
const offlineDB = new OfflineDB();

export { offlineDB, MODULES, SYNC_STATUS, STORES };